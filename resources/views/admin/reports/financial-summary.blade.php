@extends('layouts.admin')

@section('title', 'Financial Summary Report')
@section('page-title', 'Financial Summary')

@section('content')
<div class="container-fluid">
    {{-- Header Action Bar --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <p class="text-muted mb-0">High-level financial overview comparing customer sales revenue against procurement costs (COGS) to track gross profit and margins.</p>
        </div>
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <div class="btn-group btn-group-sm mr-2">
                <a href="{{ route('admin.reports.financial-summary', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'year' => now()->year]) }}" class="btn btn-outline-secondary {{ $startDate == now()->startOfMonth()->format('Y-m-d') ? 'active' : '' }}">This Month</a>
                <a href="{{ route('admin.reports.financial-summary', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d'), 'year' => now()->year]) }}" class="btn btn-outline-secondary">Last Month</a>
                <a href="{{ route('admin.reports.financial-summary', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'year' => now()->year]) }}" class="btn btn-outline-secondary {{ $startDate == now()->startOfYear()->format('Y-m-d') ? 'active' : '' }}">Year to Date</a>
            </div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card card-outline card-danger mb-4">
        <div class="card-header py-2">
            <h3 class="card-title font-weight-bold text-sm">
                <i class="fas fa-calendar-alt mr-1 text-danger"></i> Financial Period Filter
            </h3>
        </div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.reports.financial-summary') }}" class="form-row align-items-end">
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">From Date:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">To Date:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Trend Year:</label>
                    <select name="year" class="form-control form-control-sm">
                        @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <button type="submit" class="btn btn-danger btn-sm mr-2 flex-grow-1">
                        <i class="fas fa-calculator mr-1"></i> Compute Finances
                    </button>
                    <a href="{{ route('admin.reports.financial-summary') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Top Financial KPIs --}}
    <div class="row mb-4">
        {{-- Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>${{ number_format($revenue, 2) }}</h3>
                    <p class="font-weight-bold">Sales Revenue</p>
                    <small>Delivered: ${{ number_format($deliveredRevenue, 2) }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <a href="{{ route('admin.reports.sales-dashboard') }}" class="small-box-footer">
                    Sales Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- COGS (Cost of Goods Sold) --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3>${{ number_format($cogs, 2) }}</h3>
                    <p class="font-weight-bold">COGS / Purchases</p>
                    <small>Total PO Commitments: ${{ number_format($totalPurchaseCommitment, 2) }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('admin.purchase-orders.index') }}" class="small-box-footer">
                    Manage Purchases <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Gross Profit --}}
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $grossProfit >= 0 ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                <div class="inner">
                    <h3>${{ number_format($grossProfit, 2) }}</h3>
                    <p class="font-weight-bold">Gross Profit</p>
                    <small>{{ $grossProfit >= 0 ? 'Profitable Period' : 'Deficit Period' }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <span class="small-box-footer">
                    {{ $grossProfit >= 0 ? 'Net positive returns' : 'Procurement exceeds sales' }}
                </span>
            </div>
        </div>

        {{-- Gross Profit Margin --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ number_format($grossMargin, 1) }}%</h3>
                    <p class="font-weight-bold">Gross Profit Margin</p>
                    <small>Avg Order Value: ${{ number_format($avgOrderValue, 2) }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <span class="small-box-footer">
                    {{ $totalOrders }} valid orders processed
                </span>
            </div>
        </div>
    </div>

    {{-- Monthly Financial Performance Breakdown (Selected Year) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-danger">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-chart-bar mr-1 text-danger"></i> Monthly Financial Performance ({{ $year }})
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-center">Orders</th>
                                <th class="text-right">Sales Revenue</th>
                                <th class="text-right">COGS / Purchases</th>
                                <th class="text-right">Gross Profit</th>
                                <th class="text-center">Profit Margin</th>
                                <th class="text-center">Health Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyTrend as $trend)
                            <tr>
                                <td class="font-weight-bold">
                                    {{ $trend->month_name }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $trend->orders }}
                                </td>
                                <td class="text-right font-weight-bold text-primary">
                                    ${{ number_format($trend->revenue, 2) }}
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    ${{ number_format($trend->expenses, 2) }}
                                </td>
                                <td class="text-right font-weight-bold {{ $trend->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($trend->profit, 2) }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($trend->margin, 1) }}%
                                </td>
                                <td class="text-center">
                                    @if($trend->profit > 0)
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-up mr-1"></i> Profitable</span>
                                    @elseif($trend->profit < 0)
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-down mr-1"></i> Loss / Heavy Stocking</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Even / No Activity</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No monthly financial records found for {{ $year }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Cash Flow / Payments Breakdown & Top Orders --}}
    <div class="row">
        {{-- Cash Flow / Payment Methods --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-info h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-wallet mr-1 text-info"></i> Revenue Collections by Channel
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Channel</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentBreakdown as $pay)
                            <tr>
                                <td class="font-weight-bold">
                                    <i class="fas fa-check-circle text-success mr-1"></i> {{ ucfirst($pay->method) }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $pay->orders_count }}
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($pay->total_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No payment collections found in period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Revenue Orders in Period --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-star mr-1 text-warning"></i> Top Revenue Orders in Period
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topRevenueOrders as $ord)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.orders.show', $ord) }}">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $ord->shipping_name ?? ($ord->user->name ?? 'Guest') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary text-uppercase">{{ $ord->status }}</span>
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($ord->total, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No revenue orders recorded</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Purchase Costs in Period --}}
    @if(isset($topPurchaseOrders) && $topPurchaseOrders->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-outline card-secondary">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-truck-loading mr-1 text-secondary"></i> Major Purchase Expenses in Period
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">PO Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPurchaseOrders as $po)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.purchase-orders.show', $po) }}">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $po->supplier->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <small>{{ $po->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info text-uppercase">{{ $po->status }}</span>
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    ${{ number_format($po->total_amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
