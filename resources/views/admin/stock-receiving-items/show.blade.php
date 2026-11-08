@extends('layouts.admin')

@section('title', 'Receiving Item #' . $stockReceivingItem->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Receiving Item #{{ $stockReceivingItem->id }}</h1>
        <a href="{{ route('admin.stock-receivings.show', $stockReceivingItem->stockReceiving) }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Receiving
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-3">Product</dt>
                <dd class="col-9">{{ $stockReceivingItem->purchaseOrderItem->product->name ?? '—' }}</dd>

                <dt class="col-3">Variant</dt>
                <dd class="col-9">{{ $stockReceivingItem->variant->name ?? '—' }}</dd>

                <dt class="col-3">Quantity Received</dt>
                <dd class="col-9">{{ $stockReceivingItem->quantity_received }}</dd>

                <dt class="col-3">Unit Cost</dt>
                <dd class="col-9">{{ number_format($stockReceivingItem->unit_cost, 2) }}</dd>

                <dt class="col-3">Line Total</dt>
                <dd class="col-9">{{ number_format($stockReceivingItem->unit_cost * $stockReceivingItem->quantity_received, 2) }}</dd>

                <dt class="col-3">Purchase Order</dt>
                <dd class="col-9">
                    <a href="{{ route('admin.purchase-orders.show', $stockReceivingItem->stockReceiving->purchaseOrder) }}">
                        {{ $stockReceivingItem->stockReceiving->purchaseOrder->po_number ?? '—' }}
                    </a>
                </dd>

                <dt class="col-3">Stock Receiving</dt>
                <dd class="col-9">
                    <a href="{{ route('admin.stock-receivings.show', $stockReceivingItem->stockReceiving) }}">
                        #{{ $stockReceivingItem->stockReceiving->id }}
                    </a>
                </dd>

                <dt class="col-3">Purchase Order Line</dt>
                <dd class="col-9">
                    @if ($stockReceivingItem->purchaseOrderItem)
                        <a href="{{ route('admin.purchase-order-items.receiving-history', $stockReceivingItem->purchaseOrderItem) }}">
                            View full receiving history for this line item
                        </a>
                    @else
                        —
                    @endif
                </dd>
            </dl>
        </div>
    </div>
@endsection
