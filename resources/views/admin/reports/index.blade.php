@extends('layouts.admin')

@section('title', 'Business Reports & Analytics')
@section('page-title', 'Business Reports & Analytics')

@section('content')
<div class="container-fluid">
    {{-- Header Banner --}}
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted">Central reporting hub for inventory stock levels, customer sales & order fulfillment, and financial profitability.</p>
        </div>
    </div>

    {{-- Live KPI Quick Cards --}}
    <div class="row mb-4">
        {{-- Stock Value --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>${{ number_format($totalStockValue, 2) }}</h3>
                    <p class="font-weight-bold">Total Stock Valuation</p>
                    <small>{{ number_format($totalItemsInStock) }} units ({{ $lowStockCount }} low stock alerts)</small>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <a href="{{ route('admin.reports.inventory-valuation') }}" class="small-box-footer">
                    Stock Report <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Order Volume --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ number_format($totalOrders) }}</h3>
                    <p class="font-weight-bold">Total Orders Placed</p>
                    <small>All-time recorded customer orders</small>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <a href="{{ route('admin.reports.sales-dashboard') }}" class="small-box-footer">
                    Order Report <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Sales Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                    <p class="font-weight-bold">Sales Revenue</p>
                    <small>Confirmed & delivered sales</small>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.reports.sales-dashboard') }}" class="small-box-footer">
                    Sales Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Gross Profit / Financial Health --}}
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $grossProfit >= 0 ? 'bg-gradient-warning' : 'bg-gradient-danger' }}">
                <div class="inner">
                    <h3>${{ number_format($grossProfit, 2) }}</h3>
                    <p class="font-weight-bold">Net Gross Profit</p>
                    <small>Revenue minus received COGS</small>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <a href="{{ route('admin.reports.financial-summary') }}" class="small-box-footer">
                    Finance Report <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Report Navigation Cards --}}
    <div class="row">
        {{-- 1. Stock / Inventory Report --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-success card-outline h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold text-success mb-0">
                        <i class="fas fa-boxes mr-2"></i> Stock & Inventory Valuation
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Real-time status of current warehouse stock, valuation by brand and category, low-stock warnings, and historical stock movements.
                    </p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Total Stock Value & Item Counts</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Stock Breakdown by Category & Brand</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Low Stock (≤10) & Out of Stock Alerts</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Top High-Value Inventory Variants</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Stock Movements Audit Ledger</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('admin.reports.inventory-valuation') }}" class="btn btn-success btn-block font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> Open Stock Report
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. Order & Sales Dashboard --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-primary card-outline h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold text-primary mb-0">
                        <i class="fas fa-shopping-cart mr-2"></i> Sales & Orders Report
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Complete breakdown of incoming customer orders, fulfillment pipelines, daily transaction trends, payment channels, and top products.
                    </p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fas fa-check-circle text-primary mr-2"></i>Delivered vs Pending vs Cancelled Orders</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-primary mr-2"></i>Day-by-Day Sales & Order Volume Trend</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-primary mr-2"></i>Sales by Payment Channel (KHQR, Cash)</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-primary mr-2"></i>Top Selling Products & Units Sold</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-primary mr-2"></i>Recent Orders Inspection Table</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('admin.reports.sales-dashboard') }}" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> Open Orders Report
                    </a>
                </div>
            </div>
        </div>

        {{-- 3. Financial Summary --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-danger card-outline h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold text-danger mb-0">
                        <i class="fas fa-chart-line mr-2"></i> Financial Profit & Margins
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Executive profitability dashboard comparing gross sales revenue against Cost of Goods Sold (COGS) to track gross profit and margins.
                    </p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fas fa-check-circle text-danger mr-2"></i>Gross Sales Revenue vs COGS Expenses</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-danger mr-2"></i>Gross Profit & Margin Percentage (%)</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-danger mr-2"></i>Monthly Profitability Table (All 12 Months)</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-danger mr-2"></i>Major Purchase Order Costs in Period</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-danger mr-2"></i>Top Revenue Generating Orders</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('admin.reports.financial-summary') }}" class="btn btn-danger btn-block font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> Open Finance Report
                    </a>
                </div>
            </div>
        </div>

        {{-- 4. Product Performance --}}
        {{-- <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-info card-outline h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold text-info mb-0">
                        <i class="fas fa-medal mr-2"></i> Product Performance
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Analyze best selling products versus slow-moving inventory items to make smarter merchandising and purchasing decisions.
                    </p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fas fa-check-circle text-info mr-2"></i>Best Selling Products (Top 10)</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-info mr-2"></i>Slow-Moving Items with Stock & No Sales</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-info mr-2"></i>Order Frequency & Revenue by Product</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('admin.reports.product-performance') }}" class="btn btn-info btn-block font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> View Performance
                    </a>
                </div>
            </div>
        </div> --}}

        {{-- 5. Purchase Orders & Suppliers --}}
        {{-- <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-warning card-outline h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold text-warning mb-0">
                        <i class="fas fa-truck-moving mr-2"></i> Purchase Orders & Suppliers
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Monitor procurement operations, open purchase orders with suppliers, and cumulative procurement expenditure.
                    </p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fas fa-check-circle text-warning mr-2"></i>Open POs Requiring Receiving</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-warning mr-2"></i>Supplier Expenditure Performance</li>
                        <li class="mb-1"><i class="fas fa-check-circle text-warning mr-2"></i>Purchase Order Status Summary</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('admin.reports.purchase-order-summary') }}" class="btn btn-warning btn-block font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> View PO Summary
                    </a>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection
