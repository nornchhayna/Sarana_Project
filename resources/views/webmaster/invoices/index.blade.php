@extends('layouts.webmaster')

@section('title', 'Invoices')
@section('page-title', 'Billing Invoices')

@section('content')

{{-- Page Header --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Billing Invoices</h4>
                <p class="text-muted mb-0">
                    Manage and review customer invoices.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Invoice Filters
        </h3>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('webmaster.invoices.index') }}">

            <div class="row">

                {{-- Search --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Invoice #, Order #..."
                            >

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Status</label>

                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>

                            <option value="paid"
                                @selected(request('status') === 'paid')>
                                Paid
                            </option>

                            <option value="issued"
                                @selected(request('status') === 'issued')>
                                Issued
                            </option>

                            <option value="draft"
                                @selected(request('status') === 'draft')>
                                Draft
                            </option>

                            <option value="void"
                                @selected(request('status') === 'void')>
                                Void
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Date From --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_from">Issued From</label>

                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            value="{{ request('date_from') }}"
                            class="form-control"
                        >
                    </div>
                </div>

                {{-- Date To --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_to">Issued To</label>

                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            value="{{ request('date_to') }}"
                            class="form-control"
                        >
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>

                        <div class="d-flex">

                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i>
                                Search
                            </button>

                            @if(request()->anyFilled([
                                'search',
                                'status',
                                'date_from',
                                'date_to'
                            ]))
                                <a
                                    href="{{ route('webmaster.invoices.index') }}"
                                    class="btn btn-secondary"
                                >
                                    <i class="fas fa-redo mr-1"></i>
                                    Reset
                                </a>
                            @endif

                        </div>
                    </div>
                </div>

            </div>

        </form>

    </div>
</div>


{{-- Invoice Table --}}
<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-invoice mr-1"></i>
            Invoice List
        </h3>

        <div class="card-tools">
            <span class="badge badge-primary">
                {{ $invoices->total() }} Invoices
            </span>
        </div>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead class="thead-light">

                    <tr>
                        <th>Invoice Number</th>
                        <th>Order Reference</th>
                        <th>Customer</th>
                        <th>Issued Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-right">Amount Due</th>
                        <th class="text-right">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($invoices as $invoice)

                        <tr>

                            {{-- Invoice --}}
                            <td>
                                <a
                                    href="{{ route('webmaster.invoices.show', $invoice) }}"
                                    class="font-weight-bold text-primary"
                                >
                                    <i class="fas fa-file-invoice mr-1"></i>
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>

                            {{-- Order --}}
                            <td>

                                @if($invoice->order)

                                    <a
                                        href="{{ route('webmaster.orders.show', $invoice->order) }}"
                                        class="text-info font-weight-bold"
                                    >
                                        #{{ $invoice->order->order_number }}
                                    </a>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>

                            {{-- Customer --}}
                            <td>

                                <div class="font-weight-bold">
                                    {{ $invoice->order->user->name ?? 'Guest Client' }}
                                </div>

                                @if($invoice->order?->user?->email)

                                    <small class="text-muted">
                                        {{ $invoice->order->user->email }}
                                    </small>

                                @endif

                            </td>

                            {{-- Issued Date --}}
                            <td>

                                <span class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i>

                                    {{ $invoice->issued_at
                                        ? \Carbon\Carbon::parse($invoice->issued_at)->format('Y-m-d')
                                        : '—'
                                    }}
                                </span>

                            </td>

                            {{-- Due Date --}}
                            <td>

                                @if($invoice->due_at)

                                    <span class="text-muted">
                                        <i class="far fa-calendar-alt mr-1"></i>

                                        {{ \Carbon\Carbon::parse($invoice->due_at)->format('Y-m-d') }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>

                            {{-- Status --}}
                            <td>

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

                            </td>

                            {{-- Amount --}}
                            <td class="text-right">

                                <span class="font-weight-bold">
                                    ${{ number_format($invoice->order->total ?? 0, 2) }}
                                </span>

                            </td>

                            {{-- Actions --}}
                            <td class="text-right">

                                <div class="btn-group">

                                    <a
                                        href="{{ route('webmaster.invoices.show', $invoice) }}"
                                        class="btn btn-sm btn-info"
                                        title="View Invoice"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="fas fa-file-invoice fa-3x mb-3"
                                    ></i>

                                    <h5>No invoices found</h5>

                                    <p class="mb-0">
                                        No invoice records match your filters.
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
    @if($invoices->hasPages())

        <div class="card-footer clearfix">

            {{ $invoices->withQueryString()->links() }}

        </div>

    @endif

</div>

@endsection