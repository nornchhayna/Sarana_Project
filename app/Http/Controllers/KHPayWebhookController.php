<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\KHPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KHPayWebhookController extends Controller
{
    public function __construct(protected KHPayService $khpay) {}

    /**
     * Handle incoming webhook from KHPay / Bakong merchant API.
     * Route: POST /webhooks/khpay
     */
    public function handle(Request $request)
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-KHPay-Signature', '');

        // 1. Verify signature (skip when webhook_secret is empty — dev mode)
        if (! empty(config('services.khpay.webhook_secret'))) {
            if (! $this->khpay->verifyWebhookSignature($rawPayload, $signature)) {
                Log::warning('KHQR webhook: invalid signature', [
                    'ip' => $request->ip(),
                    'signature' => $signature,
                ]);

                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $data = $request->json()->all();
        $status = strtoupper($data['status'] ?? '');
        $ref = $data['ref'] ?? $data['bill_number'] ?? null;

        Log::info('KHQR webhook received', ['status' => $status, 'ref' => $ref, 'payload' => $data]);

        if (! $ref) {
            return response()->json(['error' => 'Missing ref'], 422);
        }

        return match ($status) {
            'PAID', 'SUCCESS', 'COMPLETED' => $this->handlePaymentCompleted($ref, $data),
            'FAILED', 'CANCELLED' => $this->handlePaymentFailed($ref, $data),
            default => response()->json(['message' => 'Ignored: '.$status]),
        };
    }

    protected function handlePaymentCompleted(string $ref, array $data)
    {
        $order = Order::where('payment_reference', $ref)->first();

        if (! $order) {

            Log::warning('KHQR webhook: order not found', ['ref' => $ref]);

            return response()->json(['error' => 'Order not found'], 404);

        }
        if ($order->isPaid()) {
            // Idempotency — already marked paid, acknowledge anyway
            return response()->json(['message' => 'Already paid']);
        }

        $order->update([
            'bakong_payment_status' => 'PAID',
            'status_message' => 'payment_confirmed',
            'paid_at' => now(),
        ]);

        Log::info('KHQR payment completed', ['order_id' => $order->id, 'ref' => $ref]);

        // Fire any domain events / notifications here
        // event(new OrderPaid($order));

        return response()->json(['message' => 'OK']);
    }

    protected function handlePaymentFailed(string $ref, array $data)
    {
        $order = Order::where('payment_reference', $ref)->first();

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->update([
            'bakong_payment_status' => 'FAILED',
            'status_message' => 'payment_rejected',
        ]);

        Log::info('KHQR payment failed', ['order_id' => $order->id, 'ref' => $ref]);

        // event(new OrderPaymentFailed($order));

        return response()->json(['message' => 'OK']);
    }
}
