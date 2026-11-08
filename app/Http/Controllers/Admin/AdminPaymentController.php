<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'like', '%'.$request->search.'%')
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        // Optimize totals calculation with a single query
        $totals = Payment::selectRaw('status, SUM(amount) as total')
            ->whereIn('status', ['paid', 'pending', 'refunded'])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Ensure all statuses have values
        $totals = [
            'paid' => $totals['paid'] ?? 0,
            'pending' => $totals['pending'] ?? 0,
            'refunded' => $totals['refunded'] ?? 0,
        ];

        return view('admin.payments.index', compact('payments', 'totals'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.items.product']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Payment $payment)
    {
        return $this->approve($payment);
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        app(\App\Services\OrderFulfillmentService::class)->markAsPaid($payment->order, $payment);

        $payment->refresh();
        if ($payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $payment->paid_at ?? now(),
            ]);
        }

        $payment->order->refresh();
        if ($payment->order->status === 'pending') {
            $payment->order->update([
                'status' => 'confirmed',
                'confirmed_at' => $payment->order->confirmed_at ?? now(),
            ]);
        }

        return back()->with('success', 'Payment approved and order confirmed.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be rejected.');
        }

        $payment->update(['status' => 'failed']);

        return back()->with('success', 'Payment rejected.');
    }

    public function refund(Payment $payment)
    {
        if ($payment->status !== 'paid') {
            return back()->with('error', 'Only paid payments can be refunded.');
        }

        $payment->update(['status' => 'refunded']);
        $payment->order->update(['status' => 'cancelled']);

        return back()->with('success', 'Payment refunded and order cancelled.');
    }
}
