@extends('layouts.admin')

@section('title', 'Purchase Order ' . $purchaseOrder->po_number)



@section('content')
 <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $purchaseOrder->po_number }}</h1>
        <div>
            @if ($purchaseOrder->status === 'draft')
                <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
                <form action="{{ route('admin.purchase-orders.submit', $purchaseOrder) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" onclick="return confirm('Submit this purchase order?');">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                </form>
            @endif
            @if (in_array($purchaseOrder->status, ['ordered', 'partially_received']))
                <a href="{{ route('admin.purchase-orders.receivings.create', $purchaseOrder) }}" class="btn btn-success">
                    <i class="fas fa-truck-loading"></i> Receive Stock
                </a>
                <form action="{{ route('admin.purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this purchase order?');">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            @endif
            @if ($purchaseOrder->status === 'received')
                <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">
                    <i class="fas fa-pencil-alt"></i> Edit Status
                </a>
            @endif
            <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Line Items</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th class="text-right">Ordered</th>
                                <th class="text-right">Received</th>
                                <th class="text-right">Outstanding</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '—' }}</td>
                                    <td>
                                        @if($item->variant)
                                            {{ $item->variant->color->name ?? '' }} / {{ $item->variant->phoneStorage ? ($item->variant->phoneStorage->label ?? $item->variant->phoneStorage->capacity) : '' }} / {{ $item->variant->phoneRam ? ($item->variant->phoneRam->label ?? $item->variant->phoneRam->capacity) : '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $item->quantity_ordered }}</td>
                                    <td class="text-right">{{ $item->quantity_received }}</td>
                                    <td class="text-right">
                                        @if ($item->quantity_outstanding > 0)
                                            <span class="badge badge-warning">{{ $item->quantity_outstanding }}</span>
                                        @else
                                            <span class="badge badge-success">0</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $purchaseOrder->currency }} {{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="text-right">{{ $purchaseOrder->currency }} {{ number_format($item->unit_cost * $item->quantity_ordered, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Subtotal</th>
                                <th class="text-right">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Shipping</th>
                                <th class="text-right">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->shipping_cost, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th class="text-right">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if ($purchaseOrder->stockReceivings->isNotEmpty())
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Receiving History</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-sm text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Reference</th>
                                    <th>Received At</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder->stockReceivings as $receiving)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.stock-receivings.show', $receiving) }}">#{{ $receiving->id }}</a>
                                        </td>
                                        <td>{{ $receiving->reference_number ?? '—' }}</td>
                                        <td>{{ $receiving->received_at?->format('d M Y H:i') }}</td>
                                        <td>{{ $receiving->receivedBy->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Status</dt>
                        <dd class="col-7">
                            @php
                                $badge = match ($purchaseOrder->status) {
                                    'draft' => 'secondary',
                                    'ordered' => 'info',
                                    'partially_received' => 'warning',
                                    'received' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'light',
                                };
                            @endphp
                            <span class="badge badge-{{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                            </span>
                        </dd>

                        <dt class="col-5">Supplier</dt>
                        <dd class="col-7">{{ $purchaseOrder->supplier->name ?? '—' }}</dd>

                        <dt class="col-5">Order Date</dt>
                        <dd class="col-7">{{ $purchaseOrder->order_date?->format('d M Y') }}</dd>

                        <dt class="col-5">Expected Date</dt>
                        <dd class="col-7">{{ $purchaseOrder->expected_date?->format('d M Y') ?? '—' }}</dd>

                        <dt class="col-5">Currency</dt>
                        <dd class="col-7">{{ $purchaseOrder->currency }}</dd>

                        <dt class="col-5">Created By</dt>
                        <dd class="col-7">{{ $purchaseOrder->createdBy->name ?? '—' }}</dd>
                    </dl>

                    @if ($purchaseOrder->notes)
                        <hr>
                        <strong>Notes</strong>
                        <p class="mb-0 text-muted">{{ $purchaseOrder->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection