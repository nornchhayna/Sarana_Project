@extends('layouts.admin')

@section('title', $product->name)

@section('content')
<div class="container-fluid py-4" style="max-width: 1140px;">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-secondary text-decoration-none fs-5">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h1 class="h3 fw-bold mb-0 text-dark">{{ $product->name }}</h1>
            <p class="text-muted small font-monospace mb-0">{{ $product->slug }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.show', $product->slug) }}" target="_blank"
               class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-up-right"></i> Preview
            </a>
            <a href="{{ route('admin.products.edit', $product) }}"
               class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('Permanently delete this product?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm px-3 d-flex align-items-center gap-2">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Left Column --}}
        <div class="col-lg-8 d-flex flex-column gap-4">

            {{-- Images --}}
            @php $thumbs = (array) $product->thumbnails; @endphp
            @if(count($thumbs))
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Thumbnails</h5>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($thumbs as $thumb)
                                <div class="rounded border shadow-sm" style="width: 110px; height: 110px; overflow: hidden;">
                                    <img src="{{ Storage::url($thumb) }}" alt="" class="w-100 h-100 object-fit-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Details --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Product Details</h5>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted small fw-medium">Name</div>
                        <div class="col-sm-9 fw-medium text-dark">{{ $product->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted small fw-medium">Slug</div>
                        <div class="col-sm-9 font-monospace text-secondary small">{{ $product->slug }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted small fw-medium">Token</div>
                        <div class="col-sm-9 font-monospace text-secondary small">{{ $product->token ?? '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted small fw-medium">Category</div>
                        <div class="col-sm-9 text-dark">{{ $product->category?->name ?? '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted small fw-medium">Brand</div>
                        <div class="col-sm-9 text-dark">{{ $product->brand?->name ?? '—' }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-sm-3 text-muted small fw-medium">Available From</div>
                        <div class="col-sm-9 text-dark">{{ $product->data_from?->format('M d, Y') ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($product->description)
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Description</h5>
                        <div class="text-secondary" style="line-height: 1.6;">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Status card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Status</h5>

                    <div class="d-flex justify-content-between align-items-center mb-3 text-sm">
                        <span class="text-muted small fw-medium">State</span>
                        @php
                            $badges = [
                                'active' => 'bg-success',
                                'draft' => 'bg-warning text-dark',
                                'out_of_stock' => 'bg-danger',
                            ];
                            $badgeClass = $badges[$product->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill">
                            {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 text-sm">
                        <span class="text-muted small fw-medium">Featured</span>
                        <span class="{{ $product->is_featured ? 'text-warning' : 'text-light' }} fs-5">
                            <i class="bi bi-star-fill"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 text-sm">
                        <span class="text-muted small fw-medium">Pre-order</span>
                        <span class="{{ $product->is_pre_order ? 'text-primary fw-bold' : 'text-muted' }}">
                            {{ $product->is_pre_order ? 'Yes' : 'No' }}
                        </span>
                    </div>

                   
                </div>
            </div>

            {{-- Pricing --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Pricing</h5>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted fw-medium">Price</span>
                        <span class="fw-bold">${{ number_format($product->price ?? $product->base_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted fw-medium">Discount</span>
                        <span class="{{ $product->discount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $product->discount > 0 ? $product->discount . '%' : '—' }}
                        </span>
                    </div>
                    @if($product->discount > 0)
                        <div class="d-flex justify-content-between small border-top pt-3">
                            <span class="text-muted fw-medium">Final Price</span>
                            <span class="text-primary fw-bold fs-6">${{ number_format($product->discounted_price ?? $product->sale_price, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fs-6 fw-bold border-bottom pb-3 mb-4">Performance</h5>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted fw-medium">Total Sold</span>
                        <span class="fw-bold">{{ number_format($product->total_sold) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted fw-medium">Avg Rating</span>
                        <span class="fw-bold text-warning"><i class="bi bi-star-fill me-1"></i>{{ number_format($product->avg_rating, 2) }} / 5</span>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4 small text-muted font-monospace">
                    <div class="mb-2"><strong>Created:</strong> <br> {{ $product->created_at->format('M d, Y H:i') }}</div>
                    <div class="mb-2"><strong>Updated:</strong> <br> {{ $product->updated_at->format('M d, Y H:i') }}</div>
                    @if($product->deleted_at)
                        <div class="text-danger"><strong>Deleted:</strong> <br> {{ $product->deleted_at->format('M d, Y H:i') }}</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection