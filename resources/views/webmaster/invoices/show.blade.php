@extends('layouts.webmaster')

@section('title', 'Invoice Statement Audit')
@section('page-title', 'Statement View')

@section('content')

<div class="row">

    {{-- =========================================================
         LEFT COLUMN
    ========================================================== --}}
    <div class="col-lg-8">

        {{-- Items Breakdown --}}
        <div class="card card-primary card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice mr-1"></i>
                    Items Breakdown Statement
                </h3>

                <div class="card-tools">

                    @switch($invoice->status)

                        @case('paid')
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i>
                                Paid
                            </span>
                            @break

                        @case('issued')
                            <span class="badge badge-info">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Issued
                            </span>
                            @break

                        @case('draft')
                            <span class="badge badge-secondary">
                                <i class="fas fa-file mr-1"></i>
                                Draft
                            </span>
                            @break

                        @case('void')
                            <span class="badge badge-danger">
                                <i class="fas fa-ban mr-1"></i>
                                Void
                            </span>
                            @break

                        @default
                            <span class="badge badge-secondary">
                                {{ ucfirst($invoice->status) }}
                            </span>

                    @endswitch

                </div>
            </div>

            @if($invoice->order && $invoice->order->items->count() > 0)

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover table-striped mb-0">

                            <thead class="thead-light">

                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Line Total</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($invoice->order->items as $item)

                                    <tr>

                                        {{-- Product --}}
                                        <td>

                                            <div class="font-weight-bold">
                                                {{ $item->product->name ?? 'Deleted Item Reference' }}
                                            </div>

                                            <small class="text-muted">
                                                <i class="fas fa-barcode mr-1"></i>
                                                SKU:
                                                {{ $item->product->sku ?? '—' }}
                                            </small>

                                            @if($item->color || $item->phoneStorage)

                                                <div class="mt-1">

                                                    @if($item->color)
                                                        <span class="badge badge-light">
                                                            {{ $item->color->name }}
                                                        </span>
                                                    @endif

                                                    @if($item->phoneStorage)
                                                        <span class="badge badge-light">
                                                            {{ $item->phoneStorage->capacity }}
                                                        </span>
                                                    @endif

                                                </div>

                                            @endif

                                        </td>

                                        {{-- Unit Price --}}
                                        <td class="text-center">

                                            <span class="font-weight-bold">
                                                ${{ number_format($item->price, 2) }}
                                            </span>

                                        </td>

                                        {{-- Quantity --}}
                                        <td class="text-center">

                                            <span class="badge badge-secondary">
                                                {{ $item->quantity }}
                                            </span>

                                        </td>

                                        {{-- Line Total --}}
                                        <td class="text-right">

                                            <span class="font-weight-bold">
                                                ${{ number_format($item->price * $item->quantity, 2) }}
                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @else

                <div class="card-body">

                    <div class="text-center text-muted py-4">

                        <i class="fas fa-box-open fa-3x mb-3"></i>

                        <h5>No Order Items</h5>

                        <p class="mb-0">
                            No explicit order data is linked to this invoice.
                        </p>

                    </div>

                </div>

            @endif

        </div>


        {{-- =====================================================
             INTERNAL AUDIT MEMO
        ====================================================== --}}
        @if($invoice->notes)

            <div class="card card-outline card-secondary">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-sticky-note mr-1"></i>
                        Internal Audit Memo
                    </h3>

                </div>

                <div class="card-body">

                    <div class="callout callout-info mb-0">

                        <p class="mb-0">
                            {{ $invoice->notes }}
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================================
         RIGHT COLUMN
    ========================================================== --}}
    <div class="col-lg-4">

        {{-- Billing Parameters --}}
        <div class="card card-primary card-outline">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-calculator mr-1"></i>
                    Billing Parameters
                </h3>

            </div>

            <div class="card-body">

                {{-- Invoice Number --}}
                <div class="info-box mb-3">

                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-file-invoice"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Invoice Number
                        </span>

                        <span class="info-box-number">
                            {{ $invoice->invoice_number }}
                        </span>

                    </div>

                </div>


                {{-- Details --}}
                <dl class="row">

                    {{-- Customer --}}
                    <dt class="col-sm-5">
                        Customer
                    </dt>

                    <dd class="col-sm-7">

                        {{ $invoice->order->user->name ?? 'Guest Client' }}

                    </dd>


                    {{-- Order --}}
                    <dt class="col-sm-5">
                        Order
                    </dt>

                    <dd class="col-sm-7">

                        @if($invoice->order)

                            <a
                                href="{{ route('webmaster.orders.show', $invoice->order) }}"
                                class="text-primary font-weight-bold"
                            >
                                #{{ $invoice->order->order_number }}
                            </a>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </dd>


                    {{-- Issued --}}
                    <dt class="col-sm-5">
                        Issued Date
                    </dt>

                    <dd class="col-sm-7">

                        @if($invoice->issued_at)

                            <i class="far fa-calendar-alt mr-1 text-muted"></i>

                            {{ \Carbon\Carbon::parse($invoice->issued_at)->format('Y-m-d') }}

                        @else

                            —

                        @endif

                    </dd>


                    {{-- Due --}}
                    <dt class="col-sm-5">
                        Due Date
                    </dt>

                    <dd class="col-sm-7">

                        @if($invoice->due_at)

                            <span class="text-warning">

                                <i class="far fa-calendar-alt mr-1"></i>

                                {{ \Carbon\Carbon::parse($invoice->due_at)->format('Y-m-d') }}

                            </span>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </dd>

                </dl>


                {{-- Total --}}
                <div class="callout callout-success">

                    <small class="text-muted">
                        TOTAL VOLUME DUE
                    </small>

                    <h2 class="text-success mb-0">

                        ${{ number_format($invoice->order->total ?? 0, 2) }}

                    </h2>

                </div>


                {{-- Actions --}}
                <div class="mt-3">

                    <a
                        href="{{ route('webmaster.invoices.index') }}"
                        class="btn btn-secondary btn-block"
                    >
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Invoices
                    </a>

                    @if($invoice->order)

                        <a
                            href="{{ route('webmaster.orders.show', $invoice->order) }}"
                            class="btn btn-info btn-block"
                        >
                            <i class="fas fa-shopping-cart mr-1"></i>
                            View Order
                        </a>

                    @endif

                </div>

            </div>

        </div>


        {{-- Customer Information --}}
        @if($invoice->order && $invoice->order->user)

            <div class="card card-outline card-info">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-user mr-1"></i>
                        Customer Information
                    </h3>

                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="mr-3">

                            <span
                                class="img-circle elevation-2 d-flex align-items-center justify-content-center bg-info"
                                style="width:50px;height:50px;"
                            >
                                <i class="fas fa-user text-white"></i>
                            </span>

                        </div>

                        <div>

                            <strong>
                                {{ $invoice->order->user->name }}
                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $invoice->order->user->email }}

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection