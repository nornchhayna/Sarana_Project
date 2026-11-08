@extends('layouts.admin')

@section('title', 'Purchase Order Summary')
@section('page-title', 'Purchase Order Summary')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary">
            <div class="card-body">
                <h5 class="card-title text-white">Monthly PO Value</h5>
                <h2 class="text-white">${{ number_format($monthlyPOValue, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success">
            <div class="card-body">
                <h5 class="card-title text-white">Open POs</h5>
                <h2 class="text-white">{{ $openPOs->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info">
            <div class="card-body">
                <h5 class="card-title text-white">Total Suppliers</h5>
                <h2 class="text-white">{{ $supplierPerformance->count() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- PO Status Summary --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">PO Status Summary</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($poStatusSummary->count() > 0)
                            @foreach($poStatusSummary as $status => $data)
                            <tr>
                                <td>{{ ucfirst($status) }}</td>
                                <td>{{ $data->count }}</td>
                                <td>${{ number_format($data->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center text-muted">No PO status data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Open POs --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Open Purchase Orders</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($openPOs->count() > 0)
                            @foreach($openPOs as $po)
                            <tr>
                                <td>{{ $po->po_number }}</td>
                                <td>{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td>${{ number_format($po->total_amount, 2) }}</td>
                                <td>{{ ucfirst($po->status) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">No open purchase orders</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Supplier Performance --}}
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Supplier Performance</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($supplierPerformance->count() > 0)
                            @foreach($supplierPerformance as $supplier)
                            <tr>
                                <td>{{ $supplier->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $supplier->total_orders }}</td>
                                <td>${{ number_format($supplier->total_spent, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center text-muted">No supplier performance data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
