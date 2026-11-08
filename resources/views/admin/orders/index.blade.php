@extends('layouts.admin')

@section('title', 'Sales Orders')
@section('page-title', 'Manage Orders')

@section('content')

{{-- =========================================================
     STATUS COUNTERS
========================================================= --}}

<div class="row mb-3">

    @foreach($statuses as $s)

        <div class="col-md-3 col-sm-6 mb-2">

            <a
                href="{{ route('admin.orders.index', ['status' => $s]) }}"
                class="text-decoration-none">

                <div class="card mb-0
                    {{ request('status') === $s ? 'border-primary' : '' }}">

                    <div class="card-body py-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="text-muted text-uppercase small">
                                    {{ $s }}
                                </div>

                                <h3 class="mb-0">
                                    {{ $counts[$s] ?? 0 }}
                                </h3>

                            </div>

                            <div class="text-primary">

                                <i class="fas fa-shopping-cart fa-2x"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </a>

        </div>

    @endforeach

</div>


{{-- =========================================================
     FILTERS
========================================================= --}}

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-filter mr-2"></i>
            Order Filters

        </h3>

    </div>


    <div class="card-body">

        <form method="GET">

            <div class="row align-items-end">

                {{-- Search --}}
                <div class="col-md-3">

                    <div class="form-group">

                        <label>
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search order #, customer..."
                        >

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="">
                                All Statuses
                            </option>

                            @foreach($statuses as $s)

                                <option
                                    value="{{ $s }}"
                                    @selected(request('status') === $s)>

                                    {{ ucfirst($s) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- Date From --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
                            From Date
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="form-control"
                        >

                    </div>

                </div>


                {{-- Date To --}}
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
                            To Date
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="form-control"
                        >

                    </div>

                </div>


                {{-- Filter --}}
                <div class="col-md-1">

                    <div class="form-group">

                        <button
                            type="submit"
                            class="btn btn-secondary">

                            <i class="fas fa-filter"></i>

                        </button>

                    </div>

                </div>


                {{-- Clear --}}
                @if(request()->anyFilled([
                    'search',
                    'status',
                    'date_from',
                    'date_to'
                ]))

                    <div class="col-md-1">

                        <div class="form-group">

                            <a
                                href="{{ route('admin.orders.index') }}"
                                class="btn btn-outline-secondary">

                                <i class="fas fa-times"></i>

                            </a>

                        </div>

                    </div>

                @endif


                {{-- Export --}}
                <div class="col-md-2 ml-auto">

                    <div class="form-group text-md-right">

                        <a
                            href="{{ route('admin.orders.export', request()->query()) }}"
                            class="btn btn-success">

                            <i class="fas fa-file-csv mr-1"></i>
                            Export CSV

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     ORDERS TABLE
========================================================= --}}

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-shopping-bag mr-2"></i>
            Orders

        </h3>

        <div class="card-tools">

            <span class="badge badge-secondary">

                {{ $orders->total() }}
                Orders

            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>

                        <th>
                            Order Number
                        </th>

                        <th>
                            Customer Name
                        </th>

                        <th>
                            Date Placed
                        </th>

                        <th>
                            Payment Option
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Total Bill
                        </th>

                        <th class="text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($orders as $order)

                        <tr>

                            {{-- Order Number --}}
                            <td>

                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="font-weight-bold">

                                    #{{ $order->order_number }}

                                </a>

                            </td>


                            {{-- Customer --}}
                            <td>

                                <strong>
                                    {{ $order->user->name ?? 'Guest Account' }}
                                </strong>

                            </td>


                            {{-- Date --}}
                            <td>

                                <span class="text-muted">

                                    {{ $order->created_at->format('d M Y, H:i') }}

                                </span>

                            </td>


                            {{-- Payment --}}
                            <td>

                                <span class="text-muted">

                                    {{ $order->payment->method ?? 'COD' }}

                                </span>

                            </td>


                            {{-- Status --}}
                            <td>

                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'paid' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'failed' => 'danger',
                                        'refunded' => 'secondary',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        default => 'secondary',
                                    };
                                @endphp

                                <span class="badge badge-{{ $statusClass }}">

                                    {{ ucfirst($order->status) }}

                                </span>

                            </td>


                            {{-- Total --}}
                            <td>

                                <strong>
                                    ${{ number_format($order->total, 2) }}
                                </strong>

                            </td>


                            {{-- Actions --}}
                            <td class="text-right">

                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="btn btn-info btn-sm"
                                    title="View details">

                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>

                                    <h5>
                                        No Orders Found
                                    </h5>

                                    <p class="mb-0">
                                        No orders found matching parameters.
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
    @if($orders->hasPages())

        <div class="card-footer">

            <div class="float-right">

                {{ $orders->links() }}

            </div>

        </div>

    @endif

</div>

@endsection