@extends('layouts.webmaster')

@section('title', 'Feedback Profile Statement')
@section('page-title', 'Review Audit')

@section('content')

<div class="row">

    {{-- LEFT: REVIEW CONTENT --}}
    <div class="col-lg-8">

        <div class="card card-primary card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comment-alt mr-2"></i>
                    Feedback Review Statement
                </h3>

                <div class="card-tools">
                    @if($review->is_approved)
                        <span class="badge badge-success">
                            Approved Viewable
                        </span>
                    @else
                        <span class="badge badge-warning">
                            Pending Verification
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body">

                {{-- Rating --}}
                <div class="mb-3">
                    @for($s = 1; $s <= 5; $s++)
                        @if($s <= $review->rating)
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor

                    <span class="ml-2 text-muted">
                        {{ $review->rating }} / 5
                    </span>
                </div>

                {{-- Review Title --}}
                <h4 class="font-weight-bold">
                    {{ $review->title }}
                </h4>

                {{-- Review Description --}}
                <p class="text-muted" style="line-height: 1.7;">
                    {{ $review->description ?: 'No detailed textual description provided for this rating item.' }}
                </p>

                {{-- Images --}}
                @if($review->images && count($review->images) > 0)

                    <hr>

                    <h6 class="text-uppercase text-muted font-weight-bold">
                        <i class="fas fa-images mr-1"></i>
                        Uploaded Attachments
                    </h6>

                    <div class="row mt-3">

                        @foreach($review->images as $img)

                            <div class="col-md-4 col-sm-6 mb-3">

                                <div class="card">
                                    <a href="{{ asset('storage/' . $img) }}"
                                       target="_blank">

                                        <img
                                            src="{{ asset('storage/' . $img) }}"
                                            class="card-img-top"
                                            alt="Customer Review Image"
                                            style="height: 180px; object-fit: cover;"
                                        >

                                    </a>

                                    <div class="card-body p-2 text-center">
                                        <a href="{{ asset('storage/' . $img) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">

                                            <i class="fas fa-expand mr-1"></i>
                                            View Full Size

                                        </a>
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- RIGHT: AUDIT INFORMATION --}}
    <div class="col-lg-4">

        <div class="card card-primary card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Audit Logs Ledger
                </h3>
            </div>

            <div class="card-body">

                {{-- Customer --}}
                <div class="info-box mb-3">

                    <span class="info-box-icon bg-info">
                        <i class="fas fa-user"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Customer
                        </span>

                        <span class="info-box-number">
                            {{ $review->user->name ?? 'Anonymous Account' }}
                        </span>

                    </div>

                </div>


                {{-- Product --}}
                <div class="info-box mb-3">

                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-box"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Linked Product
                        </span>

                        <span class="info-box-number text-sm">
                            {{ $review->product->name ?? '—' }}
                        </span>

                    </div>

                </div>


                {{-- Order --}}
                @if($review->order)

                    <div class="info-box mb-3">

                        <span class="info-box-icon bg-success">
                            <i class="fas fa-shopping-cart"></i>
                        </span>

                        <div class="info-box-content">

                            <span class="info-box-text">
                                Verified Order
                            </span>

                            <span class="info-box-number">

                                <a href="{{ route('webmaster.orders.show', $review->order) }}">
                                    #{{ $review->order->order_number }}
                                </a>

                            </span>

                        </div>

                    </div>

                @endif


                {{-- Submission Date --}}
                <div class="info-box mb-3">

                    <span class="info-box-icon bg-secondary">
                        <i class="fas fa-clock"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Submission Time
                        </span>

                        <span class="info-box-number text-sm">
                            {{ $review->created_at->format('Y-m-d H:i') }}
                        </span>

                    </div>

                </div>


                <hr>


                {{-- APPROVE / REVOKE --}}
                @if(!$review->is_approved)

                    <form method="POST"
                          action="{{ route('webmaster.reviews.approve', $review) }}"
                          class="mb-2">

                        @csrf
                        @method('PUT')

                        <button type="submit"
                                class="btn btn-success btn-block">

                            <i class="fas fa-check-square mr-1"></i>
                            Authorize & Publish Review

                        </button>

                    </form>

                @else

                    <form method="POST"
                          action="{{ route('webmaster.reviews.reject', $review) }}"
                          class="mb-2">

                        @csrf
                        @method('PUT')

                        <button type="submit"
                                class="btn btn-warning btn-block">

                            <i class="fas fa-ban mr-1"></i>
                            Revoke Approval Status

                        </button>

                    </form>

                @endif


                {{-- DELETE --}}
                <form method="POST"
                      action="{{ route('webmaster.reviews.destroy', $review) }}"
                      onsubmit="return confirm('Purge this feedback record from storage completely? This cannot be reversed.');"
                      class="mb-2">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger btn-block">

                        <i class="fas fa-trash-alt mr-1"></i>
                        Delete Permanently

                    </button>

                </form>


                {{-- BACK --}}
                <a href="{{ route('webmaster.reviews.index') }}"
                   class="btn btn-secondary btn-block">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Return to Registry List

                </a>

            </div>

        </div>

    </div>

</div>

@endsection