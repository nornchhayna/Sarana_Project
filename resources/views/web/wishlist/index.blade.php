@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container-xl py-5" style="padding: 20px;">

    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <span class="wishlist-count">{{ $wishlists->total() }} item{{ $wishlists->total() === 1 ? '' : 's' }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->isEmpty())
        <div class="wishlist-empty">
            <i class="fa-regular fa-heart"></i>
            <p>Nothing saved yet — items you like will show up here.</p>
            <a href="{{ route('home') }}" class="wl-btn-cart">Browse products</a>
        </div>
    @else
        <div class="wishlist-list">
            @foreach($wishlists as $item)
                @php
                    $product = $item->product;
                    if (!$product) continue;
                    $stockTotal = $product->stock ? $product->stock->sum('quantity') : 0;
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    $thumb = $product->thumbnail ?? ($images[0] ?? null);
                    $rating = round($product->avg_rating ?? 0);
                @endphp
                <div class="wl-row">
                    <div class="wl-thumb">
                        @if($thumb)
                            <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}">
                        @else
                            <span class="placeholder">📦</span>
                        @endif
                    </div>

                    <div class="wl-info">
                        <a href="{{ route('products.show', $product->slug) }}" class="wl-name">{{ $product->name }}</a>
                        <span class="wl-rating">
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </span>
                            ({{ $product->reviews_count ?? 0 }})
                        </span>
                    </div>

                    <div class="wl-meta-row">
                        <div class="wl-meta-top">
                            <span class="wl-price-current">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</span>
                            @if($product->sale_price && $product->base_price > $product->sale_price)
                                <span class="wl-price-old">${{ number_format($product->base_price, 2) }}</span>
                            @endif
                        </div>
                        @if($stockTotal > 0)
                            <span class="wl-stock in">In stock</span>
                        @else
                            <span class="wl-stock out">Out of stock</span>
                        @endif
                    </div>

                    <div class="wl-action-group">
                        <button type="button" class="wl-btn-cart" {{ $stockTotal <= 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cart-shopping"></i> Add to cart
                        </button>
                        <form action="{{ route('wishlist.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="wl-btn-remove" title="Remove from wishlist">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $wishlists->links() }}
        </div>
    @endif

      {{-- ── RELATED PRODUCTS ── --}}
    @if(isset($related) && $related->count() > 0)
    <div class="mb-5" style="margin-left: 2rem; margin-right: 2rem;">
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #ececec !important;">
            <h3 style="font-size: 24px; font-weight: 700; color: #253D4E; margin: 0;">Related Products</h3>
        </div>

        <div class="ecom-products-grid" style="margin-left: 1rem; margin-right: 1rem;">
            @foreach($related->take(4) as $relatedProduct)
                <div class="ecom-deal-card">

                    @if($relatedProduct->sale_price && $relatedProduct->base_price > $relatedProduct->sale_price)
                        @php $relDiscount = round((($relatedProduct->base_price - $relatedProduct->sale_price) / $relatedProduct->base_price) * 100); @endphp
                        <span class="ecom-deal-badge">-{{ $relDiscount }}%</span>
                    @endif

                    <div class="ecom-deal-img">
                       <a href="{{ route('products.show', $relatedProduct->slug) }}">
                            @if($relatedProduct->thumbnail)
                                <img src="{{ asset('storage/' . $relatedProduct->thumbnail) }}" alt="{{ $relatedProduct->name }}">
                            @else
                                <div style="font-size:60px; color:#adadad;">📦</div>
                            @endif
                        </a>
                    </div>

                    <span class="ecom-deal-brand">{{ $relatedProduct->brand->name ?? 'Brand' }}</span>
                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="ecom-deal-title">
                        {{ $relatedProduct->name }}
                    </a>

                    <div class="ecom-deal-rating">
                        @php $relRating = round($relatedProduct->avg_rating ?? rand(4,5)); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $relRating ? '' : 'star-empty' }}"></i>
                        @endfor
                        <span>({{ $relatedProduct->reviews_count ?? rand(10,50) }})</span>
                    </div>

                    <div class="ecom-deal-desc" title="{{ strip_tags($relatedProduct->description) }}">
                        {{ strip_tags($relatedProduct->description) ?? 'No description available for this product.' }}
                    </div>

                    <div class="ecom-deal-price">
                        <span class="ecom-deal-price-current">${{ number_format($relatedProduct->sale_price ?? $relatedProduct->base_price, 2) }}</span>
                        @if($relatedProduct->sale_price && $relatedProduct->base_price > $relatedProduct->sale_price)
                            <span class="ecom-deal-price-old">${{ number_format($relatedProduct->base_price, 2) }}</span>
                        @endif
                    </div>

                    <div class="ecom-deal-swap-area">

                        @php
                            $relSold = rand(100, 300);
                            $relAvailable = rand(50, 200);
                            $relTotal = $relSold + $relAvailable;
                            $relPercentage = ($relSold / $relTotal) * 100;
                        @endphp
                        <div class="ecom-deal-progress-wrap">
                            <div class="ecom-deal-progress-bar">
                                <div class="ecom-deal-progress-fill" style="width: {{ $relPercentage }}%;"></div>
                                <div class="ecom-deal-progress-thumb" style="left: calc({{ $relPercentage }}% - 6px);"></div>
                            </div>
                            <div class="ecom-deal-stats">
                                <div>Sold: <span>{{ $relSold }}</span></div>
                                <div>Available: <span>{{ $relAvailable }}</span></div>
                            </div>
                        </div>

                        <div class="ecom-deal-action-wrapper">
                            <form action="{{ route('cart.add') }}" method="POST" style="margin: 0; padding: 0; width: 100%;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="ecom-deal-btn-cart">
                                    <i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i> Add to Cart
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection