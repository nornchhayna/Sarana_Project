@extends('layouts.webmaster')

@section('title', 'Promotions & Coupons')
@section('page-title', 'Manage Discounts')

@section('content')

<div class="container-fluid">

    {{-- Header / Filter --}}
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="row align-items-center">

                {{-- Add Promotion --}}
                <div class="col-md-4">
                    <a href="{{ route('webmaster.promotions.create') }}"
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Promotion
                    </a>
                </div>

                {{-- Filters --}}
                <div class="col-md-8">
                    <form method="GET">
                        <div class="row justify-content-end">

                            {{-- Search --}}
                            <div class="col-md-4 mb-2">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="form-control"
                                    placeholder="Search campaign name, code..."
                                >
                            </div>

                            {{-- Type --}}
                            <div class="col-md-3 mb-2">
                                <select name="type" class="form-control">
                                    <option value="">All Value Types</option>

                                    <option value="percentage"
                                        @selected(request('type') === 'percentage')>
                                        Percentage (%)
                                    </option>

                                    <option value="fixed"
                                        @selected(request('type') === 'fixed')>
                                        Fixed Cash ($)
                                    </option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-3 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>

                                    <option value="active"
                                        @selected(request('status') === 'active')>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(request('status') === 'inactive')>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            {{-- Filter --}}
                            <div class="col-md-2 mb-2">
                                <button type="submit"
                                        class="btn btn-secondary btn-block">
                                    <i class="fas fa-filter"></i>
                                    Filter
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>


    {{-- Promotions Table --}}
    <div class="card card-outline card-primary">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tags mr-1"></i>
                Promotions & Coupons
            </h3>

            <div class="card-tools">
                <span class="badge badge-info">
                    {{ $promotions->total() }} Promotions
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead class="thead-light">
                        <tr>

                            <th style="width: 20%;">
                                Promotion / Campaign
                            </th>

                            <th>
                                Coupon Code
                            </th>

                            <th>
                                Discount Value
                            </th>

                            <th>
                                Usage
                            </th>

                            <th>
                                Validity
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-right" style="width: 120px;">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <tbody>

                        @forelse($promotions as $promo)

                            <tr>

                                {{-- Campaign Name --}}
                                <td>

                                    <strong>
                                        {{ $promo->name }}
                                    </strong>

                                    @if($promo->description)

                                        <div class="text-muted small mt-1">
                                            {{ Str::limit($promo->description, 60) }}
                                        </div>

                                    @endif

                                </td>


                                {{-- Coupon Code --}}
                                <td>

                                    @if($promo->code)

                                        <span class="badge badge-dark">
                                            <i class="fas fa-ticket-alt mr-1"></i>
                                            {{ $promo->code }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            <i class="fas fa-magic mr-1"></i>
                                            Automatic
                                        </span>

                                    @endif

                                </td>


                                {{-- Discount --}}
                                <td>

                                    @if($promo->type === 'percentage')

                                        <span class="badge badge-primary">
                                            {{ number_format($promo->value) }}%
                                        </span>

                                        <span class="text-muted">
                                            Off
                                        </span>

                                    @else

                                        <strong class="text-success">
                                            ${{ number_format($promo->value, 2) }}
                                        </strong>

                                        <span class="text-muted">
                                            Off
                                        </span>

                                    @endif

                                </td>


                                {{-- Usage --}}
                                <td>

                                    <strong>
                                        {{ $promo->usage_count ?? 0 }}
                                    </strong>

                                    <span class="text-muted">
                                        /
                                        {{ $promo->usage_limit ?: '∞' }}
                                    </span>

                                    @if($promo->usage_limit)

                                        <div class="progress mt-1"
                                             style="height: 5px; width: 100px;">

                                            @php
                                                $percentage = $promo->usage_limit > 0
                                                    ? min(
                                                        100,
                                                        (($promo->usage_count ?? 0) / $promo->usage_limit) * 100
                                                    )
                                                    : 0;
                                            @endphp

                                            <div class="progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ $percentage }}%">
                                            </div>

                                        </div>

                                    @endif

                                </td>


                                {{-- Validity --}}
                                <td>

                                    <div class="small">

                                        <div>
                                            <strong>Start:</strong>

                                            {{ $promo->starts_at
                                                ? \Carbon\Carbon::parse($promo->starts_at)->format('Y-m-d')
                                                : 'Immediate'
                                            }}
                                        </div>

                                        <div class="text-muted mt-1">
                                            <strong>End:</strong>

                                            {{ $promo->expires_at
                                                ? \Carbon\Carbon::parse($promo->expires_at)->format('Y-m-d')
                                                : 'No Expiry'
                                            }}
                                        </div>

                                    </div>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($promo->is_active)

                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>

                                    @else

                                        <span class="badge badge-secondary">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="text-right">

                                    <div class="btn-group">

                                        {{-- Edit --}}
                                        <a href="{{ route('webmaster.promotions.edit', $promo) }}"
                                           class="btn btn-sm btn-info"
                                           title="Edit Promotion">

                                            <i class="fas fa-edit"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('webmaster.promotions.destroy', $promo) }}"
                                            style="display:inline;"
                                            onsubmit="return confirm('Permanently discard this voucher promotion strategy?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Delete Promotion"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="fas fa-tags fa-3x mb-3"></i>

                                        <h5>
                                            No Promotions Found
                                        </h5>

                                        <p>
                                            No promotion metrics initialized yet.
                                        </p>

                                        <a href="{{ route('webmaster.promotions.create') }}"
                                           class="btn btn-primary">

                                            <i class="fas fa-plus"></i>
                                            Create Promotion

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($promotions->hasPages())

            <div class="card-footer clearfix">

                {{ $promotions->links() }}

            </div>

        @endif

    </div>

</div>

@endsection