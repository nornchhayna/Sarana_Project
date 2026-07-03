<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderFulfillmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Review;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── List customer orders ───────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['items', 'payment', 'invoice'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $statusCounts = Order::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('web.orders.index', compact('orders', 'statusCounts'));
    }

    // ─── View single order ──────────────────────────────────────────────────
public function show(Order $order)
{
    abort_if($order->user_id !== auth()->id(), 403);
    $order->load(['items.product', 'payment', 'invoice']);

    $steps = [
        'pending' => ['label' => 'Order Placed', 'index' => 1],
        'confirmed' => ['label' => 'Payment Verified', 'index' => 2],
        'processing' => ['label' => 'Processing', 'index' => 3],
        'shipped' => ['label' => 'Shipped', 'index' => 4],
        'delivered' => ['label' => 'Delivered', 'index' => 5],
    ];

    $currentStep = $steps[$order->status]['index'] ?? 1;

    $reviewedProductIds = \App\Models\Review::where('user_id', auth()->id())
        ->where('order_id', $order->id)
        ->pluck('product_id')
        ->toArray();

    return view('web.orders.show', compact('order', 'steps', 'currentStep', 'reviewedProductIds'));
}
    // ─── Cancel order ───────────────────────────────────────────────────────

    public function cancel(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled at its current status.');
        }

        $wasPaid = in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Restore stock only if it was already deducted after payment
        if ($wasPaid) {
            app(OrderFulfillmentService::class)->restoreStock($order);
        }

        // Mark payment as failed if pending
        $order->payments()->where('status', 'pending')->update(['status' => 'failed']);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function downloadInvoice(Order $order)
    {
        $invoice = $order->invoice;

        $pdf = Pdf::loadView('web.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function showKHQR(Order $order, BakongKHQRService $khqr)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if(! in_array($order->status, ['pending', 'confirmed']), 422);

        // Generate / retrieve QR
        $payment = $order->payments()
            ->where('method', 'khqr')
            ->where('status', 'pending')
            ->whereNotNull('khqr_md5')
            ->where(fn ($q) => $q->whereNull('khqr_expires_at')->orWhere('khqr_expires_at', '>', now()))
            ->first();

        if (! $payment) {
            $qrData = $khqr->generateForOrder($order);

            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => 'khqr',
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => config('bakong.currency') === 840 ? 'USD' : 'KHR',
                'khqr_string' => $qrData['qr_string'],
                'khqr_md5' => $qrData['md5'],
                'khqr_expires_at' => Carbon::now()->addMinutes(15),
            ]);
        }

        return view('orders.khqr-pay', [
            'order' => $order,
            'qrString' => $payment->khqr_string,
            'md5' => $payment->khqr_md5,
            'expiresAt' => $payment->khqr_expires_at?->toIso8601String(),
        ]);
    }
}
