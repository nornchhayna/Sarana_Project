<?php

namespace App\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;

class WebmasterInvoiceController extends Controller
{
     public function index(Request $request)
    {
        $query = Invoice::with(['order.user']);

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%'.$request->search.'%')
                ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', '%'.$request->search.'%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('issued_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issued_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('webmaster.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'order.user',
            'order.items.product',
            'order.items.color',
            'order.items.phoneStorage',
            'order.payment',
            'order.promotion',
        ]);

        return view('webmaster.invoices.show', compact('invoice'));
    }

    public function generate(Order $order)
    {
        if ($order->invoice) {
            return redirect()->route('webmaster.invoices.show', $order->invoice)
                ->with('info', 'Invoice already exists.');
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'status' => $order->payment?->status === 'paid' ? 'paid' : 'issued',
        ]);

        return redirect()->route('webmaster.invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,issued,paid,void',
        ]);

        $invoice->update($validated);

        return back()->with('success', 'Invoice status updated.');
    }

    public function void(Invoice $invoice)
    {
        if ($invoice->status === 'void') {
            return back()->with('error', 'Invoice is already voided.');
        }

        $invoice->update(['status' => 'void']);

        return back()->with('success', 'Invoice voided.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load([
            'order.user',
            'order.items.product',
            'order.items.color',
            'order.items.phoneStorage',
            'order.payment',
        ]);

        return view('webmaster.invoices.print', compact('invoice'));
    }
}
