@extends('layouts.admin')

@section('title', $supplier->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $supplier->name }}</h1>
        <div>
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning">
                <i class="fas fa-pencil-alt"></i> Edit
            </a>
            <form action="{{ route('admin.suppliers.toggleActive', $supplier) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $supplier->is_active ? 'secondary' : 'success' }}">
                    <i class="fas fa-power-off"></i> {{ $supplier->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Recent Purchase Orders</h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">{{ $supplier->purchase_orders_count }} total</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPurchaseOrders as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.purchase-orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td>{{ $po->order_date?->format('d M Y') }}</td>
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
                                    <td class="text-right">{{ $po->currency }} {{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No purchase orders yet.</td>
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
                        <dt class="col-5">Status</dt>
                        <dd class="col-7">
                            <span class="badge badge-{{ $supplier->is_active ? 'success' : 'secondary' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>

                        <dt class="col-5">Contact Person</dt>
                        <dd class="col-7">{{ $supplier->contact_person ?? '—' }}</dd>

                        <dt class="col-5">Phone</dt>
                        <dd class="col-7">{{ $supplier->phone ?? '—' }}</dd>

                        <dt class="col-5">Email</dt>
                        <dd class="col-7">{{ $supplier->email ?? '—' }}</dd>

                        <dt class="col-5">Address</dt>
                        <dd class="col-7">{{ $supplier->address ?? '—' }}</dd>

                        <dt class="col-5">City</dt>
                        <dd class="col-7">{{ $supplier->city ?? '—' }}</dd>

                        <dt class="col-5">Country</dt>
                        <dd class="col-7">{{ $supplier->country ?? '—' }}</dd>
                    </dl>

                    @if ($supplier->notes)
                        <hr>
                        <strong>Notes</strong>
                        <p class="mb-0 text-muted">{{ $supplier->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
