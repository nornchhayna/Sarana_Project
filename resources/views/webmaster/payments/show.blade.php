@extends('layouts.webmaster')

@section('title', 'Ledger Transaction Audit')
@section('page-title', 'Transaction Statement')

@section('content')

<div class="row">

    {{-- =========================================================
         LEFT COLUMN
    ========================================================== --}}
    <div class="col-lg-8">

        {{-- =====================================================
             ITEMS SNAPSHOT
        ====================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-receipt mr-2"></i>
                    Items Snapshot Statement
                </h3>

                <div class="card-tools">

                    @php
                        $statusClass = match($payment->status) {
                            'paid' => 'success',
                            'pending' => 'warning',
                            'refunded' => 'info',
                            'failed' => 'danger',
                            default => 'secondary',
                        };
                    @endphp

                    <span class="badge badge-{{ $statusClass }}">
                        {{ ucfirst($payment->status) }}
                    </span>

                </div>

            </div>


            @if($payment->order && $payment->order->items->count() > 0)

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th class="text-center">
                                        Unit Price
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

                                @foreach($payment->order->items as $item)

                                    <tr>

                                        {{-- Product --}}
                                        <td>

                                            <strong>
                                                {{ $item->product->name ?? 'Deleted Item Reference' }}
                                            </strong>

                                            <div class="small text-muted mt-1">

                                                SKU:
                                                {{ $item->product->sku ?? '—' }}

                                            </div>

                                        </td>


                                        {{-- Price --}}
                                        <td class="text-center">

                                            ${{ number_format($item->price, 2) }}

                                        </td>


                                        {{-- Quantity --}}
                                        <td class="text-center">

                                            <strong>
                                                {{ $item->quantity }}
                                            </strong>

                                        </td>


                                        {{-- Total --}}
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

            @else

                <div class="card-body text-center py-5">

                    <i class="fas fa-link-slash fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        No Order Information
                    </h5>

                    <p class="text-muted mb-0">

                        No explicit order tracking dependencies
                        linked to this payment.

                    </p>

                </div>

            @endif

        </div>


        {{-- =====================================================
             CLIENT & SETTLEMENT
        ====================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-user mr-2"></i>
                    Client & Settlement Properties

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    {{-- Customer --}}
                    <div class="col-md-6">

                        <div class="text-muted text-uppercase small mb-2">

                            Payer Customer Account

                        </div>

                        <h5 class="mb-1">

                            {{ $payment->order->user->name ?? 'Guest Client' }}

                        </h5>

                        <p class="text-muted mb-0">

                            <i class="fas fa-envelope mr-1"></i>

                            {{ $payment->order->user->email ?? '—' }}

                        </p>

                    </div>


                    {{-- Order --}}
                    <div class="col-md-6 mt-4 mt-md-0">

                        <div class="text-muted text-uppercase small mb-2">

                            Order Connections

                        </div>

                        @if($payment->order)

                            <div class="mb-2">

                                <span class="text-muted">
                                    Order Tracker:
                                </span>

                                <a
                                    href="{{ route('webmaster.orders.show', $payment->order) }}"
                                    class="font-weight-bold">

                                    #{{ $payment->order->order_number }}

                                </a>

                            </div>


                            <div>

                                <span class="text-muted">
                                    Workflow Phase:
                                </span>

                                <span class="badge badge-secondary">

                                    {{ strtoupper($payment->order->status) }}

                                </span>

                            </div>

                        @else

                            <div class="text-muted">

                                No relative order metadata binding found.

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RIGHT COLUMN
    ========================================================== --}}
    <div class="col-lg-4">

        {{-- =====================================================
             LEDGER PARAMETERS
        ====================================================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Ledger Parameters

                </h3>

            </div>


            <div class="card-body">

                {{-- Reference --}}
                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Reference ID
                    </span>

                    <strong class="text-right">

                        {{ $payment->transaction_id ?: 'MANUAL_DEPOSIT' }}

                    </strong>

                </div>


                {{-- Method --}}
                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Method Channel
                    </span>

                    <strong>

                        {{ $payment->method ?? 'COD' }}

                    </strong>

                </div>


                {{-- Created --}}
                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Ingestion Date
                    </span>

                    <strong>

                        {{ $payment->created_at->format('Y-m-d H:i') }}

                    </strong>

                </div>


                {{-- Paid At --}}
                @if($payment->paid_at)

                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Captured Date
                        </span>

                        <strong class="text-success">

                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}

                        </strong>

                    </div>

                @endif


                <hr>


                {{-- Amount --}}
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <span class="font-weight-bold">
                        Total Volume
                    </span>

                    <span
                        class="text-primary font-weight-bold"
                        style="font-size: 20px;">

                        ${{ number_format($payment->amount, 2) }}

                    </span>

                </div>


                {{-- =================================================
                     PENDING PAYMENT ACTIONS
                ================================================== --}}
                @if($payment->status === 'pending')

                    {{-- Approve --}}
                    <form
                        method="POST"
                        action="{{ route('webmaster.payments.approve', $payment) }}"
                        class="mb-3">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn btn-success btn-block">

                            <i class="fas fa-check-circle mr-1"></i>

                            Authorize & Capture Payment

                        </button>

                    </form>


                    {{-- Reject --}}
                    <form
                        method="POST"
                        action="{{ route('webmaster.payments.reject', $payment) }}">

                        @csrf
                        @method('PUT')


                        <div class="form-group">

                            <label>
                                Rejection Reason
                            </label>

                            <input
                                type="text"
                                name="reason"
                                class="form-control"
                                placeholder="Optional rationale details..."
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-danger btn-block">

                            <i class="fas fa-times-circle mr-1"></i>

                            Refuse / Reject Processing

                        </button>

                    </form>


                {{-- =================================================
                     PAID PAYMENT ACTIONS
                ================================================== --}}
                @elseif($payment->status === 'paid')

                    <form
                        method="POST"
                        action="{{ route('webmaster.payments.refund', $payment) }}"
                        onsubmit="return confirm('Execute automated payment volume reversal ledger item and terminate linked orders processing stage?');">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn btn-danger btn-block">

                            <i class="fas fa-undo mr-1"></i>

                            Process Reversal Refund

                        </button>

                    </form>

                @endif


                {{-- Back --}}
                <a
                    href="{{ route('webmaster.payments.index') }}"
                    class="btn btn-secondary btn-block mt-3">

                    <i class="fas fa-arrow-left mr-1"></i>

                    Transactions Ledger

                </a>

            </div>

        </div>

    </div>

</div>

@endsection