@extends('layouts.webmaster')

@section('title', 'Product Reviews')
@section('page-title', 'Customer Feedback')

@section('content')

{{-- =========================
    REVIEW STATISTICS
========================= --}}
<div class="row">

    {{-- Pending --}}
    <div class="col-lg-4 col-md-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Awaiting Approval</p>
            </div>

            <div class="icon">
                <i class="fas fa-comments"></i>
            </div>
        </div>
    </div>

    {{-- Approved --}}
    <div class="col-lg-4 col-md-6 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['approved'] }}</h3>
                <p>Approved Reviews</p>
            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    {{-- Average Rating --}}
    <div class="col-lg-4 col-md-6 col-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>
                    {{ number_format($stats['avg'], 1) }}
                    <small>/ 5</small>
                </h3>
                <p>Average Store Rating</p>
            </div>

            <div class="icon">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>

</div>


{{-- =========================
    FILTERS
========================= --}}
<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Review Filters
        </h3>
    </div>

    <div class="card-body">

        <form method="GET">

            <div class="row">

                {{-- Search --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Search</label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search title, user, item..."
                        >
                    </div>
                </div>

                {{-- Rating --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Rating</label>

                        <select name="rating" class="form-control">
                            <option value="">All Ratings</option>

                            @for($i = 5; $i >= 1; $i--)
                                <option
                                    value="{{ $i }}"
                                    @selected(request('rating') == $i)
                                >
                                    {{ $i }} Stars
                                </option>
                            @endfor

                        </select>
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
                                value="approved"
                                @selected(request('status') === 'approved')
                            >
                                Approved
                            </option>

                            <option
                                value="pending"
                                @selected(request('status') === 'pending')
                            >
                                Pending
                            </option>

                        </select>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="col-md-4">
                    <label>&nbsp;</label>

                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>

                        @if(request()->anyFilled([
                            'search',
                            'rating',
                            'status'
                        ]))

                            <a
                                href="{{ route('webmaster.reviews.index') }}"
                                class="btn btn-secondary"
                            >
                                <i class="fas fa-times"></i>
                                Clear
                            </a>

                        @endif

                    </div>
                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================
    REVIEWS TABLE
========================= --}}
<form
    id="bulk-approve-form"
    method="POST"
    action="{{ route('webmaster.reviews.bulkApprove') }}"
>

    @csrf

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-comments mr-1"></i>
                Customer Reviews
            </h3>

            @if($reviews->count() > 0)

                <div class="card-tools">

                    <button
                        type="button"
                        class="btn btn-success btn-sm"
                        onclick="submitBulkApprove()"
                    >
                        <i class="fas fa-check-double"></i>
                        Approve Selected
                    </button>

                </div>

            @endif

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead class="thead-light">

                        <tr>

                            <th style="width: 45px;">

                                <input
                                    type="checkbox"
                                    id="select-all"
                                >

                            </th>

                            <th>Product</th>

                            <th>Customer</th>

                            <th>Rating</th>

                            <th>Review</th>

                            <th>Status</th>

                            <th class="text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reviews as $review)

                            <tr>

                                {{-- Checkbox --}}
                                <td>

                                    <input
                                        type="checkbox"
                                        name="ids[]"
                                        value="{{ $review->id }}"
                                        class="bulk-select"
                                    >

                                </td>


                                {{-- Product --}}
                                <td>

                                    <strong>
                                        {{ $review->product->name ?? 'Deleted Item' }}
                                    </strong>

                                </td>


                                {{-- Customer --}}
                                <td>

                                    {{ $review->user->name ?? 'Anonymous' }}

                                </td>


                                {{-- Rating --}}
                                <td>

                                    <span class="text-warning">

                                        @for($s = 1; $s <= 5; $s++)

                                            @if($s <= $review->rating)

                                                <i class="fas fa-star"></i>

                                            @else

                                                <i class="far fa-star"></i>

                                            @endif

                                        @endfor

                                    </span>

                                    <small class="text-muted ml-1">
                                        {{ $review->rating }}/5
                                    </small>

                                </td>


                                {{-- Review --}}
                                <td style="max-width: 350px;">

                                    <strong>
                                        {{ $review->title }}
                                    </strong>

                                    <br>

                                    <span
                                        class="text-muted"
                                        title="{{ $review->description }}"
                                    >
                                        {{ Str::limit($review->description, 80) }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($review->is_approved)

                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            Approved
                                        </span>

                                    @else

                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i>
                                            Pending
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="text-right">

                                    <div class="btn-group">

                                        {{-- View --}}
                                        <a
                                            href="{{ route('webmaster.reviews.show', $review) }}"
                                            class="btn btn-sm btn-info"
                                            title="View Review"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>


                                        {{-- Approve --}}
                                        @if(!$review->is_approved)

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success"
                                                title="Approve Review"
                                                onclick="approveReview({{ $review->id }})"
                                            >
                                                <i class="fas fa-check"></i>
                                            </button>

                                        @endif


                                        {{-- Delete --}}
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            title="Delete Review"
                                            onclick="deleteReview({{ $review->id }})"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="fas fa-comments fa-2x mb-3"
                                    ></i>

                                    <br>

                                    No customer reviews found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($reviews->hasPages())

            <div class="card-footer clearfix">

                {{ $reviews->links() }}

            </div>

        @endif

    </div>

</form>


{{-- =========================
    HIDDEN APPROVE FORMS
========================= --}}
@foreach($reviews as $review)

    @if(!$review->is_approved)

        <form
            id="approve-form-{{ $review->id }}"
            action="{{ route('webmaster.reviews.approve', $review) }}"
            method="POST"
            style="display:none;"
        >

            @csrf
            @method('PUT')

        </form>

    @endif


    {{-- Delete --}}
    <form
        id="delete-form-{{ $review->id }}"
        action="{{ route('webmaster.reviews.destroy', $review) }}"
        method="POST"
        style="display:none;"
    >

        @csrf
        @method('DELETE')

    </form>

@endforeach


{{-- =========================
    JAVASCRIPT
========================= --}}
@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | Select / Unselect All
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('select-all')
        ?.addEventListener('change', function () {

            document
                .querySelectorAll('.bulk-select')
                .forEach(function (checkbox) {

                    checkbox.checked = this.checked;

                }, this);

        });


    /*
    |--------------------------------------------------------------------------
    | Bulk Approve
    |--------------------------------------------------------------------------
    */

    function submitBulkApprove()
    {
        const selected = document.querySelectorAll(
            '.bulk-select:checked'
        );

        if (selected.length === 0) {

            alert('Please select at least one review.');

            return;

        }

        if (
            confirm(
                'Approve all selected reviews?'
            )
        ) {

            document
                .getElementById('bulk-approve-form')
                .submit();

        }
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Single Review
    |--------------------------------------------------------------------------
    */

    function approveReview(id)
    {
        if (
            confirm(
                'Approve this review?'
            )
        ) {

            document
                .getElementById(
                    'approve-form-' + id
                )
                .submit();

        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Review
    |--------------------------------------------------------------------------
    */

    function deleteReview(id)
    {
        if (
            confirm(
                'Permanently delete this review? This action cannot be undone.'
            )
        ) {

            document
                .getElementById(
                    'delete-form-' + id
                )
                .submit();

        }
    }

</script>

@endpush

@endsection