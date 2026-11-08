<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/invoices
     * List all invoices belonging to the authenticated user.
     * (Added — not in the original web controller, but a Flutter app needs
     * a listing screen before it can view/download a single invoice.)
     */
    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['order' => fn ($q) => $q->select('id', 'order_number', 'user_id', 'total', 'status', 'created_at')])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'invoices' => collect($invoices->items())->map(fn ($i) => $this->transformInvoiceSummary($i)),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
            ],
        ]);
    }

    /**
     * GET /api/invoices/{invoice}
     * Full invoice detail as JSON (replaces the Blade "show" view).
     */
    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        abort_if($invoice->order->user_id !== $request->user()->id, 403, 'This invoice does not belong to you.');

        $invoice->load(['order.items.product', 'order.payment', 'order.user']);

        return response()->json([
            'success' => true,
            'invoice' => $this->transformInvoiceDetail($invoice),
        ]);
    }

    /**
     * GET /api/invoices/{invoice}/download
     * Streams the PDF bytes directly — Flutter's http package can save the
     * response body straight to a file (or open it in a PDF viewer package).
     */
    public function download(Request $request, Invoice $invoice): Response
    {
        abort_if($invoice->order->user_id !== $request->user()->id, 403, 'This invoice does not belong to you.');

        $invoice->load(['order.items.product', 'order.payment', 'order.user']);

        $pdf = app('dompdf.wrapper')->loadView('web.invoices.pdf', compact('invoice'));

        // ->download() sends the file with Content-Disposition: attachment,
        // which works fine for Flutter's http.get() — the raw bytes come
        // back in response.bodyBytes and can be written to local storage.
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    // ─────────────────────────────────────────────────────────────
    // Transformers
    // ─────────────────────────────────────────────────────────────

    private function transformInvoiceSummary(Invoice $invoice): array
    {
        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'order_number' => $invoice->order->order_number ?? null,
            'total' => (float) ($invoice->order->total ?? 0),
            'status' => $invoice->order->status ?? null,
            'created_at' => $invoice->created_at->toIso8601String(),
            'created_at_human' => $invoice->created_at->diffForHumans(),
            'download_url' => route('api.v1.invoices.download', $invoice),
        ];
    }

    private function transformInvoiceDetail(Invoice $invoice): array
    {
        $order = $invoice->order;

        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'created_at' => $invoice->created_at->toIso8601String(),

            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total' => (float) $order->total,
                'created_at' => $order->created_at->toIso8601String(),
            ],

            'customer' => [
                'name' => $order->user->name ?? null,
                'email' => $order->user->email ?? null,
            ],

            'payment' => $order->payment ? [
                'method' => $order->payment->method ?? null,
                'status' => $order->payment->status ?? null,
                'paid_at' => $order->payment->paid_at
                    ? \Illuminate\Support\Carbon::parse($order->payment->paid_at)->toIso8601String()
                    : null,
                'transaction_id' => $order->payment->transaction_id ?? null,
            ] : null,

            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product->name ?? $item->product_name ?? null,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => round($item->unit_price * $item->quantity, 2),
            ]),

            'download_url' => route('api.v1.invoices.download', $invoice),
        ];
    }
}