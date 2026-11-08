@extends('layouts.admin')

@section('title', 'Stock Movement #' . $movement->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Movement #{{ $movement->id }}</h1>
        <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-body">
            @php
                $isInbound = str_ends_with($movement->type, '_in') || $movement->type === 'purchase_receipt' || $movement->type === 'return';
            @endphp
            <dl class="row mb-0">
                <dt class="col-3">Date</dt>
                <dd class="col-9">{{ $movement->created_at?->format('d M Y H:i') }}</dd>

                <dt class="col-3">Product</dt>
                <dd class="col-9">{{ $movement->product?->name ?? $movement->variant?->product?->name ?? '—' }}</dd>

                <dt class="col-3">Variant / Type</dt>
                <dd class="col-9">
                    @if ($movement->variant)
                        {{ $movement->variant->color->name ?? '' }} 
                        {{ $movement->variant->phoneStorage?->capacity ?? '' }}
                        {{ $movement->variant->phoneRam?->capacity ?? '' }}
                        @if($movement->variant->sku)
                            <span class="text-muted">({{ $movement->variant->sku }})</span>
                        @endif
                    @else
                        <span class="badge badge-secondary">Single Product</span>
                        @if($movement->product?->sku)
                            <span class="text-muted">({{ $movement->product->sku }})</span>
                        @endif
                    @endif
                </dd>

                <dt class="col-3">Type</dt>
                <dd class="col-9">
                    <span class="badge badge-{{ $isInbound ? 'success' : 'danger' }}">
                        {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                    </span>
                </dd>

                <dt class="col-3">Quantity</dt>
                <dd class="col-9">{{ $isInbound ? '+' : '−' }}{{ $movement->quantity }}</dd>

                <dt class="col-3">Reference</dt>
                <dd class="col-9">
                    @if ($reference && $movement->reference_type === 'stock_receiving')
                        <a href="{{ route('admin.stock-receivings.show', $reference) }}">
                            Stock Receiving #{{ $reference->id }}
                        </a>
                    @elseif ($reference && $movement->reference_type === 'purchase_order')
                        <a href="{{ route('admin.purchase-orders.show', $reference) }}">
                            Purchase Order {{ $reference->po_number }}
                        </a>
                    @elseif ($movement->reference_type)
                        <span class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', $movement->reference_type)) }}
                            @if ($movement->reference_id)
                                #{{ $movement->reference_id }}
                            @endif
                        </span>
                    @else
                        —
                    @endif
                </dd>

                <dt class="col-3">Recorded By</dt>
                <dd class="col-9">{{ $movement->createdBy->name ?? '—' }}</dd>

                <dt class="col-3">Notes</dt>
                <dd class="col-9">{{ $movement->notes ?: '—' }}</dd>
            </dl>
        </div>
    </div>
@endsection
