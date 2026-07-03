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

        $totals = [
            'paid' => Payment::where('status', 'paid')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->sum('amount'),
            'refunded' => Payment::where('status', 'refunded')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'totals'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.items.product']);

        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Update order status
        $payment->order->update(['status' => 'confirmed']);

        // Auto-generate invoice if not exists
        if (! $payment->order->invoice) {
            Invoice::create([
                'order_id' => $payment->order->id,
                'invoice_number' => 'INV-'.str_pad($payment->order->id, 6, '0', STR_PAD_LEFT),
                'issued_at' => now()->toDateString(),
                'due_at' => now()->addDays(7)->toDateString(),
                'status' => 'paid',
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
