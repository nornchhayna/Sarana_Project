<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Services\OrderFulfillmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Services\BakongKHQRService;
use Carbon\Carbon;
use App\Models\Merchantbakongkhqr;

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
        $order->load(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']);

        $steps = [
            'pending' => ['label' => 'Order Placed', 'index' => 1],
            'confirmed' => ['label' => 'Payment Verified', 'index' => 2],
            'processing' => ['label' => 'Processing', 'index' => 3],
            'shipped' => ['label' => 'Shipped', 'index' => 4],
            'delivered' => ['label' => 'Delivered', 'index' => 5],
        ];

        $currentStep = $steps[$order->status]['index'] ?? 1;

        $reviewedProductIds = Review::where('user_id', auth()->id())
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

 // add this import at the top

public function showKHQR(Order $order, BakongKHQRService $khqr)
{
    abort_if($order->user_id !== auth()->id(), 403);
    abort_if(! in_array($order->status, ['pending', 'confirmed']), 422);

    $payment = $order->payments()
        ->where('method', 'khqr')
        ->where('status', 'pending')
        ->whereNotNull('khqr_md5')
        ->where(fn ($q) => $q->whereNull('khqr_expires_at')->orWhere('khqr_expires_at', '>', now()))
        ->first();

    if (! $payment) {
        $bakong = Merchantbakongkhqr::first();

        if ($bakong && ! empty($bakong->bakongAccountID)) {
            $khqr->setMerchantConfig($bakong);
        }

        $qrData = $khqr->generateKHQR([
            'amount'         => $order->total,
            'ref'            => $order->order_number,
            'ttl_seconds'    => BakongKHQRService::DEFAULT_TTL_SECONDS,
            'store_label'    => config('bakong.merchant_name', 'Online Store'),
            'terminal_label' => 'Online',
        ]);

        if (! $qrData['success']) {
            return back()->with('error', $qrData['error'] ?? 'Unable to generate payment QR code.');
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'khqr',
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => (int) config('bakong.currency', 840) === 840 ? 'USD' : 'KHR',
            'khqr_string' => $qrData['qr_string'],
            'khqr_md5' => $qrData['md5'],
            'khqr_expires_at' => Carbon::now()->addSeconds(BakongKHQRService::DEFAULT_TTL_SECONDS),
        ]);
    }

    $khqrData = [
        'success' => true,
        'qr_string' => $payment->khqr_string,
        'md5' => $payment->khqr_md5,
        'error' => null,
    ];

    return view('web.payment.index', compact('order', 'khqrData'));
}
}
