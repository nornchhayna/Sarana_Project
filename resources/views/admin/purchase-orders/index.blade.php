@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Purchase Orders</h1>
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Purchase Order
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form action="{{ route('admin.purchase-orders.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Search PO #</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="PO-2026-0001">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Supplier</label>
                    <select name="supplier_id" class="form-control form-control-sm">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All statuses</option>
                        @foreach (['draft', 'ordered', 'partially_received', 'received', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-sm btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>PO #</th>
                        <th>Supplier</th>
                        <th>Order Date</th>
                        <th>Expected</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr>
                            <td>
                                <a href="{{ route('admin.purchase-orders.show', $po) }}">{{ $po->po_number }}</a>
                            </td>
                            <td>{{ $po->supplier->name ?? '—' }}</td>
                            <td>{{ $po->order_date?->format('d M Y') }}</td>
                            <td>{{ $po->expected_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $po->items_count }}</td>
                            <td>{{ $po->currency }} {{ number_format($po->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $badge = match ($po->status) {
                                        'draft' => 'secondary',
                                        'ordered' => 'info',
                                        'partially_received' => 'warning',
                                        'received' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($po->status === 'draft')
                                    <a href="{{ route('admin.purchase-orders.edit', $po) }}" class="btn btn-xs btn-warning">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.destroy', $po) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
@endsection