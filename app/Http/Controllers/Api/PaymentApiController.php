<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\BakongKHQRService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function initiate(Request $request, BakongKHQRService $khqr): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        abort_if($order->user_id !== $request->user()->id, 403);

        return app(OrderApiController::class)->showKHQR($request, $order, $khqr);
    }

    public function initiateCambodia(Request $request, BakongKHQRService $khqr): JsonResponse
    {
        $orderId = $request->input('order_id');
        if ($orderId) {
            $order = Order::where('id', $orderId)
                ->orWhere('order_number', $orderId)
                ->first();
            if ($order) {
                return app(OrderApiController::class)->showKHQR($request, $order, $khqr);
            }
        }

        $amount = (float) $request->input('amount', 0);
        $ref = (string) ($request->input('reference') ?? ('REF-' . strtoupper(uniqid())));
        $qrData = $khqr->generateFallbackKHQR($amount, $ref);

        return response()->json([
            'success' => true,
            'qr_code' => $qrData['qr_string'],
            'qr_string' => $qrData['qr_string'],
            'md5' => $qrData['md5'],
            'reference' => $ref,
            'amount' => $amount,
            'currency' => 'USD',
            'data' => $qrData,
        ]);
    }

    public function checkCambodiaStatus(Request $request, string $reference, BakongKHQRService $khqr): JsonResponse
    {
        $payment = Payment::where('khqr_md5', $reference)
            ->orWhere('transaction_id', $reference)
            ->orWhereHas('order', fn ($q) => $q->where('order_number', $reference)->orWhere('id', $reference))
            ->latest()
            ->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'is_paid' => false,
                'status' => 'not_found',
                'message' => 'Payment not found',
            ], 404);
        }

        $order = $payment->order;
        $isPaid = in_array($payment->status, ['paid', 'completed'], true) || ($order && $order->is_paid);

        // Use cache to avoid repeated external API calls
        $cacheKey = "payment_status_{$payment->id}_{$payment->khqr_md5}";

        if (! $isPaid && ! empty($payment->khqr_md5)) {
            try {
                // Check cache first - only cache positive results
                $cachedStatus = cache()->get($cacheKey);

                if ($cachedStatus === true) {
                    $isPaid = true;
                } else {
                    // External API call - always check if not already confirmed as paid
                    $checkResult = $khqr->checkByMd5Verbose($payment->khqr_md5);
                    
                    Log::info('Payment status check', [
                        'payment_id' => $payment->id,
                        'md5' => $payment->khqr_md5,
                        'check_result' => $checkResult,
                    ]);

                    if ($checkResult['paid']) {
                        $isPaid = true;
                        // Cache the positive result for 5 minutes
                        cache()->put($cacheKey, true, 300);

                        try {
                            $payment->update(['status' => 'paid', 'paid_at' => now()]);
                        } catch (\Throwable $e) {
                            $payment->update(['status' => 'completed', 'paid_at' => now()]);
                        }
                        $order?->update(['status' => 'confirmed', 'confirmed_at' => now()]);
                        
                        Log::info('Payment marked as paid', [
                            'payment_id' => $payment->id,
                            'order_id' => $order?->id,
                        ]);
                    }
                    // Don't cache negative results - always recheck to avoid missing payments
                }
            } catch (\Throwable $e) {
                // Ignore errors, return current status
                Log::error('Payment status check failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'is_paid' => $isPaid,
            'paid' => $isPaid,
            'status' => $isPaid ? 'paid' : $payment->status,
            'payment_status' => $isPaid ? 'paid' : $payment->status,
            'order_status' => $order?->status,
            'data' => [
                'is_paid' => $isPaid,
                'status' => $isPaid ? 'paid' : $payment->status,
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
            ],
        ]);
    }

    public function verify(Request $request, Payment $payment, BakongKHQRService $khqr): JsonResponse
    {
        return $this->checkCambodiaStatus($request, $payment->khqr_md5 ?? (string) $payment->order_id, $khqr);
    }

    public function callback(Request $request): JsonResponse
    {
        $reference = $request->input('md5')
            ?? $request->input('reference')
            ?? $request->input('order_number');

        if ($reference) {
            $payment = Payment::where('khqr_md5', $reference)
                ->orWhereHas('order', fn ($q) => $q->where('order_number', $reference))
                ->latest()
                ->first();

            if ($payment) {
                try {
                    $payment->update(['status' => 'paid', 'paid_at' => now()]);
                } catch (\Throwable $e) {
                    $payment->update(['status' => 'completed', 'paid_at' => now()]);
                }
                $payment->order?->update(['status' => 'confirmed', 'confirmed_at' => now()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Callback processed',
        ]);
    }
}
