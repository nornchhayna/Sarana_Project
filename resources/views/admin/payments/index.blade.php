@extends('layouts.admin')

@section('title', 'Payment Transactions')
@section('page-title', 'Billing & Transactions')

@section('content')

{{-- =========================================================
     REVENUE STATISTICS
========================================================= --}}
<div class="row">

    {{-- Paid --}}
    <div class="col-lg-4 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>
                    ${{ number_format($totals['paid'], 2) }}
                </h3>

                <p>Captured Revenue</p>
            </div>

            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>


    {{-- Pending --}}
    <div class="col-lg-4 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>
                    ${{ number_format($totals['pending'], 2) }}
                </h3>

                <p>Escrow Pending</p>
            </div>

            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>


    {{-- Refunded --}}
    <div class="col-lg-4 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>
                    ${{ number_format($totals['refunded'], 2) }}
                </h3>

                <p>Total Reversed / Refunded</p>
            </div>

            <div class="icon">
                <i class="fas fa-undo"></i>
            </div>
        </div>
    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================= --}}
<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Search Transactions
        </h3>

    </div>


    <div class="card-body">

        <form method="GET" action="{{ route('admin.payments.index') }}">

            <div class="row">

                {{-- Search --}}
                <div class="col-md-3">

                    <div class="form-group">

                        <label>
                            Transaction / Order
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search Tx ID, order number..."
                        >

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>Status</label>

                        <select name="status" class="form-control">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="paid"
                                @selected(request('status') === 'paid')>
                                Paid
                            </option>

                            <option
                                value="pending"
                                @selected(request('status') === 'pending')>
                                Pending
                            </option>

                            <option
                                value="refunded"
                                @selected(request('status') === 'refunded')>
                                Refunded
                            </option>

                            <option
                                value="failed"
                                @selected(request('status') === 'failed')>
                                Failed
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Payment Method --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>Payment Method</label>

                        <select name="method" class="form-control">

                            <option value="">
                                All Methods
                            </option>

                            <option
                                value="Stripe"
                                @selected(request('method') === 'Stripe')>
                                Stripe
                            </option>

                            <option
                                value="PayPal"
                                @selected(request('method') === 'PayPal')>
                                PayPal
                            </option>

                            <option
                                value="Bank Transfer"
                                @selected(request('method') === 'Bank Transfer')>
                                Bank Transfer
                            </option>

                            <option
                                value="COD"
                                @selected(request('method') === 'COD')>
                                COD
                            </option>

                        </select>

                    </div>

                </div>


                {{-- From --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>From Date</label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="form-control"
                        >

                    </div>

                </div>


                {{-- To --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>To Date</label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="form-control"
                        >

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="col-md-1">

                    <div class="form-group">

                        <label>&nbsp;</label>

                        <button
                            type="submit"
                            class="btn btn-primary btn-block"
                            title="Search">

                            <i class="fas fa-search"></i>

                        </button>

                    </div>

                </div>

            </div>


            {{-- Reset --}}
            @if(request()->anyFilled([
                'search',
                'status',
                'method',
                'date_from',
                'date_to'
            ]))

                <a
                    href="{{ route('admin.payments.index') }}"
                    class="btn btn-secondary">

                    <i class="fas fa-sync-alt mr-1"></i>
                    Reset Filters

                </a>

            @endif

        </form>

    </div>

</div>


{{-- =========================================================
     TRANSACTIONS TABLE
========================================================= --}}
<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-credit-card mr-2"></i>
            Payment Transactions

        </h3>

        <div class="card-tools">

            <span class="badge badge-primary">
                {{ $payments->total() }} Transactions
            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>

                        <th>
                            Transaction ID
                        </th>

                        <th>
                            Order
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Payment Method
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-right">
                            Amount
                        </th>

                        <th class="text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($payments as $payment)

                        <tr>

                            {{-- Transaction ID --}}
                            <td>

                                <a
                                    href="{{ route('admin.payments.show', $payment) }}"
                                    class="font-weight-bold text-primary">

                                    {{ $payment->transaction_id ?: 'UNASSIGNED_REF' }}

                                </a>

                            </td>


                            {{-- Order --}}
                            <td>

                                @if($payment->order)

                                    <a
                                        href="{{ route('admin.orders.show', $payment->order) }}"
                                        class="font-weight-bold">

                                        #{{ $payment->order->order_number }}

                                    </a>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- Customer --}}
                            <td>

                                <strong>

                                    {{ $payment->order->user->name ?? 'Guest Client' }}

                                </strong>

                            </td>


                            {{-- Payment Method --}}
                            <td>

                                @php
                                    $methodIcon = match($payment->method) {
                                        'Stripe' => 'fa-credit-card',
                                        'PayPal' => 'fa-paypal',
                                        'Bank Transfer' => 'fa-building-columns',
                                        'COD' => 'fa-money-bill-wave',
                                        default => 'fa-wallet',
                                    };
                                @endphp

                                <i class="fas {{ $methodIcon }} mr-1 text-muted"></i>

                                {{ $payment->method ?? 'COD' }}

                            </td>


                            {{-- Date --}}
                            <td>

                                <span class="text-muted">

                                    {{ $payment->created_at->format('Y-m-d H:i') }}

                                </span>

                            </td>


                            {{-- Status --}}
                            <td>

                                @php

                                    $badgeClass = match($payment->status) {

                                        'paid' => 'success',

                                        'pending' => 'warning',

                                        'refunded' => 'info',

                                        'failed' => 'danger',

                                        default => 'secondary',

                                    };

                                @endphp


                                <span class="badge badge-{{ $badgeClass }}">

                                    {{ ucfirst($payment->status) }}

                                </span>

                            </td>


                            {{-- Amount --}}
                            <td class="text-right">

                                <strong>

                                    ${{ number_format($payment->amount, 2) }}

                                </strong>

                            </td>


                            {{-- Actions --}}
                            <td class="text-right">

                                <a
                                    href="{{ route('admin.payments.show', $payment) }}"
                                    class="btn btn-sm btn-info"
                                    title="View Transaction">

                                    <i class="fas fa-file-invoice-dollar"></i>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="fas fa-receipt fa-3x mb-3 d-block">
                                    </i>

                                    <h5>
                                        No transaction records found
                                    </h5>

                                    <p class="mb-0">
                                        Try changing your search filters.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    @if($payments->hasPages())

        <div class="card-footer clearfix">

            {{ $payments->links() }}

        </div>

    @endif

</div>

@endsection