@extends('layouts.app')
@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h2 class="text-primary fw-bold">INVOICE</h2>
                            <p class="mb-0">#{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0">{{ config('app.name') }}</h4>
                            <p class="text-muted">Phnom Penh, Cambodia</p>
                        </div>
                    </div>

                    <hr>

                    {{-- Billing Info --}}
                    <div class="row mb-4">
                        <div class="col-6">
                            <h6 class="text-uppercase text-muted">Bill To</h6>
                            <p class="fw-bold mb-0">{{ $invoice->order->user->name }}</p>
                            <p class="text-muted">{{ $invoice->order->shipping_city }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <h6 class="text-uppercase text-muted">Date</h6>
                            <p>{{ $invoice->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totals --}}
                    <div class="d-flex justify-content-end mt-4">
                        <div class="w-25">
                            <div class="d-flex justify-content-between">
                                <span>Total:</span>
                                <strong>${{ number_format($invoice->order->total, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-primary px-4">Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection