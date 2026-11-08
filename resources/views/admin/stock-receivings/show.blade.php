@extends('layouts.admin')

@section('title', 'Stock Receiving #' . $stockReceiving->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Receiving #{{ $stockReceiving->id }}</h1>
        <div>
            <a href="{{ route('admin.purchase-orders.show', $stockReceiving->purchaseOrder) }}" class="btn btn-default">
                <i class="fas fa-file-invoice"></i> View Purchase Order
            </a>
            <a href="{{ route('admin.stock-movements.index', ['reference_type' => 'stock_receiving']) }}" class="btn btn-default">
                <i class="fas fa-exchange-alt"></i> Stock Movements
            </a>
            <a href="{{ route('admin.stock-receivings.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Items Received</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th class="text-right">Qty Received</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Line Total</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockReceiving->items as $item)
                                <tr>
                                    <td>{{ $item->purchaseOrderItem->product->name ?? '—' }}</td>
                                    <td>
                                        @if($item->variant)
                                            {{ $item->variant->color->name ?? '' }} / {{ $item->variant->phoneStorage ? ($item->variant->phoneStorage->label ?? $item->variant->phoneStorage->capacity) : '' }} / {{ $item->variant->phoneRam ? ($item->variant->phoneRam->label ?? $item->variant->phoneRam->capacity) : '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $item->quantity_received }}</td>
                                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->unit_cost * $item->quantity_received, 2) }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.stock-receiving-items.show', $item) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-right">
                                    {{ number_format($stockReceiving->items->sum(fn ($i) => $i->unit_cost * $i->quantity_received), 2) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-secondary mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> Stock Movements Logged</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th class="text-right">Quantity</th>
                                <th>Date</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stockReceiving->movements as $m)
                                <tr>
                                    <td>
                                        <strong>{{ $m->product?->name ?? $m->variant?->product?->name ?? '—' }}</strong>
                                        @if ($m->variant)
                                            <span class="text-muted small ml-1">
                                                ({{ $m->variant->color?->name }} / {{ $m->variant->phoneStorage?->capacity }})
                                            </span>
                                        @else
                                            <span class="badge badge-secondary ml-1">Single Product</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $m->type)) }}</span></td>
                                    <td class="text-right font-weight-bold text-success">+{{ $m->quantity }}</td>
                                    <td>{{ $m->created_at?->format('d M Y H:i') }}</td>
                                    <td>{{ $m->createdBy->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No movements recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Purchase Order</dt>
                        <dd class="col-7">
                            <a href="{{ route('admin.purchase-orders.show', $stockReceiving->purchaseOrder) }}">
                                {{ $stockReceiving->purchaseOrder->po_number ?? '—' }}
                            </a>
                        </dd>

                        <dt class="col-5">Supplier</dt>
                        <dd class="col-7">{{ $stockReceiving->supplier->name ?? '—' }}</dd>

                        <dt class="col-5">Reference #</dt>
                        <dd class="col-7">{{ $stockReceiving->reference_number ?? '—' }}</dd>

                        <dt class="col-5">Received At</dt>
                        <dd class="col-7">{{ $stockReceiving->received_at?->format('d M Y H:i') }}</dd>

                        <dt class="col-5">Received By</dt>
                        <dd class="col-7">{{ $stockReceiving->receivedBy->name ?? '—' }}</dd>
                    </dl>

                    @if ($stockReceiving->notes)
                        <hr>
                        <strong>Notes</strong>
                        <p class="mb-0 text-muted">{{ $stockReceiving->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
