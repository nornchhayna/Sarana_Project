<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Download / view invoice ────────────────────────────────────────────

    public function show(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user via its order
        abort_if($invoice->order->user_id !== auth()->id(), 403);

        $invoice->load(['order.items.product', 'order.payment', 'order.user']);

        return view('web.invoices.show', compact('invoice'));
    }

    // ─── Download invoice as PDF ─────────────────────────────────────────────

    public function download(Invoice $invoice)
    {
        abort_if($invoice->order->user_id !== auth()->id(), 403);

        $invoice->load(['order.items.product', 'order.payment', 'order.user']);

        $pdf = app('dompdf.wrapper')->loadView('web.invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
