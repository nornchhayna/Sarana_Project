@extends('layouts.admin')

@section('title', 'Order Invoice Details')
@section('page-title', "Order #{$order->order_number}")

@section('content')

<div class="row">

    {{-- =====================================================
         LEFT PANEL
    ====================================================== --}}
    <div class="col-lg-8">

        {{-- =================================================
             ITEMS
        ================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-shopping-cart mr-2"></i>
                    Items Overview

                </h3>

                <div class="card-tools">

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

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th class="text-center">
                                    Price
                                </th>

                                <th class="text-center">
                                    Qty
                                </th>

                                <th class="text-right">
                                    Line Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($order->items as $item)

                                <tr>

                                    {{-- Product --}}
                                    <td>

                                        <strong>
                                            {{ $item->product->name ?? 'Deleted Item' }}
                                        </strong>

                                        @if($item->color || $item->phoneStorage)

                                            <div class="small text-muted mt-1">

                                                <i class="fas fa-sliders-h mr-1"></i>

                                                Variant:
                                                {{ $item->color->name ?? '' }}
                                                {{ $item->phoneStorage->capacity ?? '' }}

                                            </div>

                                        @endif

                                    </td>


                                    {{-- Price --}}
                                    <td class="text-center">

                                        ${{ number_format($item->price, 2) }}

                                    </td>


                                    {{-- Quantity --}}
                                    <td class="text-center">

                                        <span class="font-weight-bold">
                                            {{ $item->quantity }}
                                        </span>

                                    </td>


                                    {{-- Line Total --}}
                                    <td class="text-right">

                                        <strong>

                                            ${{ number_format(
                                                $item->price * $item->quantity,
                                                2
                                            ) }}

                                        </strong>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =================================================
             CUSTOMER / BILLING INFORMATION
        ================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-user mr-2"></i>
                    Delivery Profiles & Billing

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    {{-- Customer --}}
                    <div class="col-md-6">

                        <div class="text-muted text-uppercase small mb-2">
                            Customer Profile
                        </div>

                        <h5 class="mb-1">

                            {{ $order->user->name ?? 'Guest Client' }}

                        </h5>

                        <div class="text-muted">

                            {{ $order->user->email ?? '—' }}

                        </div>

                    </div>


                    {{-- Meta --}}
                    <div class="col-md-6 mt-3 mt-md-0">

                        <div class="text-muted text-uppercase small mb-2">
                            Meta Indexes
                        </div>

                        <div class="mb-2">

                            <span class="text-muted">
                                Placed Time:
                            </span>

                            <strong>
                                {{ $order->created_at->format('Y-m-d H:i') }}
                            </strong>

                        </div>

                        <div>

                            <span class="text-muted">
                                Invoice Tracking ID:
                            </span>

                            <strong class="text-primary">

                                {{ $order->invoice->invoice_number ?? 'Drafting' }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         RIGHT PANEL
    ====================================================== --}}
    <div class="col-lg-4">

        {{-- =================================================
             FINANCIAL SUMMARY
        ================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-calculator mr-2"></i>
                    Finances Summary

                </h3>

            </div>


            <div class="card-body">

                {{-- Subtotal --}}
                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Subtotal:
                    </span>

                    <strong>

                        ${{ number_format(
                            $order->items->sum(
                                fn($i) => $i->price * $i->quantity
                            ),
                            2
                        ) }}

                    </strong>

                </div>


                {{-- Promotion --}}
                @if($order->promotion)

                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">

                            Promo
                            ({{ $order->promotion->code }}):

                        </span>

                        <strong class="text-danger">

                            - ${{ number_format(
                                $order->discount_amount ?? 0,
                                2
                            ) }}

                        </strong>

                    </div>

                @endif


                <hr>


                {{-- Grand Total --}}
                <div class="d-flex justify-content-between align-items-center">

                    <span class="font-weight-bold">
                        Grand Total
                    </span>

                    <span class="text-primary font-weight-bold"
                          style="font-size: 20px;">

                        ${{ number_format($order->total, 2) }}

                    </span>

                </div>

            </div>

        </div>


        {{-- =================================================
             STATUS UPDATE
        ================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-exchange-alt mr-2"></i>
                    Update Order Status

                </h3>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('admin.orders.updateStatus', $order) }}">

                    @csrf
                    @method('PUT')


                    {{-- Status --}}
                    <div class="form-group">

                        <label for="status">
                            Set Stage Workflow
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-control"
                            required>

                            @foreach($statuses as $s)

                                <option
                                    value="{{ $s }}"
                                    @selected($order->status === $s)>

                                    {{ ucfirst($s) }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Note --}}
                    <div class="form-group">

                        <label for="note">
                            Operation Progress Log Note
                        </label>

                        <textarea
                            name="note"
                            id="note"
                            class="form-control"
                            rows="4"
                            placeholder="Optional progress notification audit message trails..."
                        ></textarea>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary btn-block">

                        <i class="fas fa-shield-alt mr-1"></i>

                        Update Status Transition

                    </button>

                </form>

            </div>

        </div>


        {{-- =================================================
             CANCEL / REVERSE
        ================================================== --}}
        @if(!in_array($order->status, ['delivered', 'cancelled']))

            <div class="card card-danger">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Danger Zone

                    </h3>

                </div>


                <div class="card-body">

                    <p class="text-muted small">

                        This will cancel the order and restore the
                        related inventory stock records.

                    </p>


                    <form
                        method="POST"
                        action="{{ route('admin.orders.cancel', $order) }}"
                        onsubmit="return confirm('Revoke invoice processing and restore tracking item inventories stock records immediately?');">

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-danger btn-block">

                            <i class="fas fa-ban mr-1"></i>

                            Terminate & Reverse Order Stock

                        </button>

                    </form>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection