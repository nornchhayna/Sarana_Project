@extends('layouts.admin')

@section('title', 'Stock Receivings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Receivings</h1>

        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-primary">
            <i class="fas fa-truck-loading"></i> Manage Purchase Orders
        </a>
    </div>

    @if(isset($totalCount))
        <div class="alert alert-info">
            Total stock receivings in database: {{ $totalCount }} (Showing {{ $receivings->count() }} on this page)
        </div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form action="{{ route('admin.stock-receivings.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="mb-1">Purchase Order</label>
                    <input type="text" name="purchase_order_id" value="{{ request('purchase_order_id') }}"
                           class="form-control form-control-sm" placeholder="PO ID">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="mb-1">Supplier</label>
                    <input type="text" name="supplier_id" value="{{ request('supplier_id') }}"
                           class="form-control form-control-sm" placeholder="Supplier ID">
                </div>
                <div class="col-md-4 mb-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.stock-receivings.index') }}" class="btn btn-sm btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Purchase Order</th>
                        <th>Supplier</th>
                        <th>Reference</th>
                        <th>Received At</th>
                        <th>Received By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receivings as $receiving)
                        <tr>
                            <td>#{{ $receiving->id }}</td>
                            <td>
                                <a href="{{ route('admin.purchase-orders.show', $receiving->purchaseOrder) }}">
                                    {{ $receiving->purchaseOrder->po_number ?? '—' }}
                                </a>
                            </td>
                            <td>{{ $receiving->supplier->name ?? '—' }}</td>
                            <td>{{ $receiving->reference_number ?? '—' }}</td>
                            <td>{{ $receiving->received_at?->format('d M Y H:i') }}</td>
                            <td>{{ $receiving->receivedBy->name ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.stock-receivings.show', $receiving) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stock receivings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $receivings->links() }}
        </div>
    </div>
@endsection