@extends('layouts.admin')

@section('title', 'Stock Movements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Movements</h1>
        <a href="{{ route('admin.stock-movements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Record Adjustment
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form action="{{ route('admin.stock-movements.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Search Product / SKU</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Product name, SKU, notes..." class="form-control form-control-sm">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="mb-1">Type</label>
                    <select name="type" class="form-control form-control-sm">
                        <option value="">All types</option>
                        @foreach (['adjustment_in', 'adjustment_out', 'purchase_receipt', 'sale', 'sale_return', 'damage'] as $type)
                            <option value="{{ $type }}" @selected(request('type') === $type)>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="mb-1">Reference</label>
                    <select name="reference_type" class="form-control form-control-sm">
                        <option value="">All references</option>
                        <option value="stock_receiving" @selected(request('reference_type') === 'stock_receiving')>Stock Receiving</option>
                        <option value="purchase_order" @selected(request('reference_type') === 'purchase_order')>Purchase Order</option>
                        <option value="order" @selected(request('reference_type') === 'order')>Order</option>
                        <option value="manual_adjustment" @selected(request('reference_type') === 'manual_adjustment')>Manual Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="mb-1">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 mb-2">
                    <label class="mb-1">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 mb-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-sm btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product / Item</th>
                        <th>Type</th>
                        <th class="text-right">Quantity</th>
                        <th>Reference</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at?->format('d M Y H:i') }}</td>
                            <td>
                                <strong>{{ $movement->product?->name ?? $movement->variant?->product?->name ?? '—' }}</strong>
                                @if ($movement->variant)
                                    <div class="text-muted small">
                                        @if ($movement->variant->color)
                                            <span class="badge badge-light">{{ $movement->variant->color->name }}</span>
                                        @endif
                                        @if ($movement->variant->phoneStorage)
                                            <span class="badge badge-light">{{ $movement->variant->phoneStorage->capacity }}</span>
                                        @endif
                                        @if ($movement->variant->phoneRam)
                                            <span class="badge badge-light">{{ $movement->variant->phoneRam->capacity }}</span>
                                        @endif
                                        @if ($movement->variant->sku)
                                            <span class="ml-1 text-monospace text-muted">[{{ $movement->variant->sku }}]</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-muted small">
                                        <span class="badge badge-secondary"><i class="fas fa-box mr-1"></i> Single Product</span>
                                        @if ($movement->product?->sku)
                                            <span class="ml-1 text-monospace text-muted">[{{ $movement->product->sku }}]</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $isInbound = str_ends_with($movement->type, '_in') || $movement->type === 'purchase_receipt' || $movement->type === 'return';
                                @endphp
                                <span class="badge badge-{{ $isInbound ? 'success' : 'danger' }}">
                                    {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                {{ $isInbound ? '+' : '−' }}{{ $movement->quantity }}
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ ucfirst(str_replace('_', ' ', $movement->reference_type ?? '—')) }}
                                    @if ($movement->reference_id)
                                        #{{ $movement->reference_id }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($movement->notes, 40) ?: '—' }}</td>
                            <td>{{ $movement->createdBy->name ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.stock-movements.show', $movement) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No stock movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
