@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@push('styles')
<style>
    /* ── BASE THEME VARIABLES ── */
    :root {
        --clr-primary: #0056b3;
        --clr-dark: #2B3D5D;
        --clr-body: #4F5D77;
        --clr-muted: #9CA3AF;
        --clr-border: #E5E7EB;
        --clr-sale: #E74C3C;
        --clr-bg: #F9FAFB;
        --radius-md: 8px;
        --radius-sm: 4px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    /* ── BREADCRUMB ── */
    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--clr-muted);
        margin-bottom: 24px;
    }
    .breadcrumb-custom a {
        color: var(--clr-muted);
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-custom a:hover {
        color: var(--clr-primary);
    }
    .breadcrumb-custom .sep {
        color: var(--clr-border);
    }

    /* ── 2 COLUMN GRID ── */
    .product-details-wrapper {
        background: #fff;
        border-radius: var(--radius-md);
        padding: 40px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--clr-border);
        margin-bottom: 40px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: 5fr 7fr;   /* Similar to your original 5-7 ratio */
        gap: 50px;
    }

    @media (max-width: 992px) {
        .product-grid {
            grid-template-columns: 1fr;
            gap: 35px;
        }
    }
.btn-buy-now {
    background: var(--clr-primary, #FF6B00);
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  
        height: 48px;
        background: var(--clr-primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
}
.btn-buy-now:hover {
    background: #004494;
}
.btn-buy-now:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
    /* ── GALLERY ── */
    .gallery-main {
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-md);
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
        background: #fff;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .gallery-main img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .gallery-thumbs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .thumb-btn {
        width: 80px;
        height: 80px;
        border: 2px solid transparent;
        border-radius: var(--radius-sm);
        background: #fff;
        padding: 5px;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .thumb-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .thumb-btn.active, .thumb-btn:hover {
        border-color: var(--clr-primary);
    }

    /* All your other original styles remain unchanged below */
    .badge-hot, .badge-preorder {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        color: #fff;
        background: var(--clr-sale);
        letter-spacing: 0.5px;
    }
    .badge-preorder { background: #8E338A; }

    .prod-brand {
        font-size: 13px;
        text-transform: uppercase;
        color: var(--clr-muted);
        letter-spacing: 1px;
        margin-bottom: 5px;
        font-weight: 600;
    }
    .prod-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--clr-dark);
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .prod-rating {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 14px;
        color: var(--clr-muted);
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--clr-border);
    }
    .stars { color: #F59E0B; font-size: 13px; }
    .prod-sold {
        border-left: 1px solid var(--clr-border);
        padding-left: 15px;
    }
    .prod-sold strong { color: var(--clr-dark); }

    .price-box {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    .price-current {
        font-size: 32px;
        font-weight: 700;
        color: var(--clr-sale);
    }
    .price-old {
        font-size: 18px;
        color: var(--clr-muted);
        text-decoration: line-through;
    }
    .badge-discount {
        background: #FEF2F2;
        color: var(--clr-sale);
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 13px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
        font-size: 14px;
        color: var(--clr-dark);
    }
    .info-list li {
        margin-bottom: 8px;
        display: flex;
    }
    .info-list .label {
        width: 120px;
        color: var(--clr-muted);
    }

    .variant-selectors {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 10px;
    }
    .variant-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .variant-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--clr-muted);
        min-width: 60px;
    }
    .variant-swatches {
        display: flex;
        gap: 8px;
    }
    .variant-swatch {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--clr-border);
        cursor: pointer;
        padding: 0;
        transition: border-color 0.15s ease, transform 0.15s ease;
    }
    .variant-swatch:hover {
        transform: scale(1.1);
    }
    .variant-swatch.is-selected {
        border-color: var(--clr-dark);
        box-shadow: 0 0 0 2px #fff inset;
    }
    .variant-storage-options {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .variant-storage-option {
        font-size: 13px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--clr-border);
        background: #fff;
        color: var(--clr-dark);
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }
    .variant-storage-option:hover {
        border-color: var(--clr-muted);
    }
    .variant-storage-option.is-selected {
        border-color: var(--clr-dark);
        background-color: var(--clr-dark);
        color: #fff;
    }
    .variant-error {
        font-size: 13px;
        font-weight: 600;
        color: var(--clr-sale);
    }

    .action-area {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid var(--clr-border);
    }
    .qty-selector {
        display: flex;
        align-items: center;
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-sm);
        height: 48px;
        background: #fff;
    }
    .qty-btn {
        width: 40px;
        height: 100%;
        background: transparent;
        border: none;
        font-size: 18px;
        color: var(--clr-dark);
        cursor: pointer;
        transition: background 0.2s;
    }
    .qty-btn:hover { background: var(--clr-bg); }
    .qty-input {
        width: 50px;
        height: 100%;
        border: none;
        border-left: 1px solid var(--clr-border);
        border-right: 1px solid var(--clr-border);
        text-align: center;
        font-weight: 600;
        color: var(--clr-dark);
        -moz-appearance: textfield;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .btn-add-cart {
        flex-grow: 1;
        height: 48px;
        background: var(--clr-primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-add-cart:hover:not(:disabled) {
        background: #004494;
    }
    .btn-add-cart:active:not(:disabled) {
        transform: scale(0.98);
    }
    .btn-add-cart:disabled {
        background: var(--clr-muted);
        cursor: not-allowed;
    }

    .btn-wishlist {
        width: 48px;
        height: 48px;
        border: 1px solid var(--clr-border);
        background: #fff;
        color: var(--clr-dark);
        border-radius: var(--radius-sm);
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-wishlist:hover {
        border-color: var(--clr-sale);
        color: var(--clr-sale);
        background: #FEF2F2;
    }

    /* ── RESPONSIVE DESIGN ── */
    @media (max-width: 768px) {
        .action-area { flex-wrap: wrap; }
        .qty-selector { width: 100%; justify-content: space-between; }
        .qty-input { width: 100%; }
        .btn-add-cart, .btn-wishlist { flex: 1; }
    }
    @media (max-width: 576px) {
        .prod-rating { flex-direction: column; align-items: flex-start; gap: 5px; }
        .prod-sold { border-left: none; padding-left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-xl">

    {{-- ── BREADCRUMB ── --}}
    <nav class="breadcrumb-custom">
        <a href="{{ route('home') }}">Home</a>
        <span class="sep"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></span>
        <a href="{{ route('products.index') }}">Products</a>

        @if($product->category)
            <span class="sep"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></span>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif

        <span class="sep"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></span>
        <span style="color: var(--clr-dark);">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ── MAIN PRODUCT BLOCK (2 GRID) ── --}}
    <div class="product-details-wrapper">
        <div class="product-grid">

            {{-- Left: Image Gallery --}}
            <div>
                @php
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    $images = is_array($images) ? $images : [];

                    if ($product->thumbnail) {
                        array_unshift($images, $product->thumbnail);
                    }
                    $images = array_unique($images);
                @endphp

                <div class="gallery-main">
                    @if(count($images) > 0)
                        <img id="main-image" src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}">
                    @else
                        <div style="font-size: 80px; color: var(--clr-border);">📦</div>
                    @endif
                </div>

                @if(count($images) > 1)
                    <div class="gallery-thumbs">
                        @foreach($images as $index => $img)
                            <button class="thumb-btn {{ $index === 0 ? 'active' : '' }}" onclick="changeMainImage(this, '{{ asset('storage/' . $img) }}')">
                                <img src="{{ asset('storage/' . $img) }}" alt="Thumbnail {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Product Details --}}
            <div>

                {{-- Top Badges --}}
                <div class="d-flex gap-2 mb-3">
                    @if($product->is_featured) <span class="badge-hot">⭐ FEATURED</span> @endif
                    @if($product->is_pre_order) <span class="badge-preorder">PRE-ORDER</span> @endif
                    @if($product->is_new) <span class="badge-hot" style="background: var(--clr-primary);">NEW</span> @endif
                </div>

                {{-- Title & Brand --}}
                <div class="prod-brand">{{ $product->brand->name ?? 'UNKNOWN BRAND' }}</div>
                <h1 class="prod-title">{{ $product->name }}</h1>

                {{-- Rating & Sold Info --}}
                <div class="prod-rating">
                    <div class="stars">
                        @php $rating = round($product->avg_rating ?? 0); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rating) <i class="fa-solid fa-star"></i>
                            @else <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span>({{ $product->reviews_count ?? 0 }} Reviews)</span>

                    <div class="prod-sold">
                        <strong>{{ number_format($product->total_sold ?? 0) }}</strong> Sold
                    </div>

                    @if($product->sku)
                        <div class="prod-sold">
                            SKU: <strong>{{ $product->sku }}</strong>
                        </div>
                    @endif
                </div>

                {{-- Price Box --}}
                <div class="price-box">
                    <div class="price-current">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</div>

                    @if($product->sale_price && $product->base_price > 0)
                        <div class="price-old">${{ number_format($product->base_price, 2) }}</div>
                        @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                        <div class="badge-discount">-{{ $discount }}% OFF</div>
                    @endif
                </div>

                {{-- Info List --}}
                <ul class="info-list">
                    @if($product->phoneModel)
                        <li><span class="label">Model:</span> {{ $product->phoneModel->name }}</li>
                    @endif

                    <li><span class="label">Availability:</span>
                        @php $stockTotal = $product->stock ? $product->stock->sum('quantity') : 0; @endphp
                        @if($stockTotal > 0)
                            <strong style="color: var(--clr-sale);">In Stock ({{ $stockTotal }} Units)</strong>
                        @else
                            <strong style="color: #E74C3C;">Out of Stock</strong>
                        @endif
                    </li>

                    @if($product->data_from)
                        <li><span class="label">Available From:</span> {{ \Carbon\Carbon::parse($product->data_from)->format('M d, Y') }}</li>
                    @endif

                    @if($product->token)
                        <li><span class="label">Token:</span> <code>{{ $product->token }}</code></li>
                    @endif
                </ul>

                {{-- Short Description --}}
                @if($product->description)
                    <p style="font-size: 14px; color: var(--clr-body); line-height: 1.6; margin-bottom: 0;">
                        {!! nl2br(e(Str::limit($product->description, 300))) !!}
                    </p>
                @endif

                {{-- Variant Selectors --}}
                @if($product->colors->count() || $product->storages->count())
                    <div class="variant-selectors" data-product-id="{{ $product->id }}">

                        @if($product->colors->count())
                            <div class="variant-group">
                                <span class="variant-label">Color</span>
                                <div class="variant-swatches" role="radiogroup" aria-label="Color">
                                    @foreach($product->colors as $color)
                                        <button
                                            type="button"
                                            class="variant-swatch"
                                            style="background-color: {{ $color->hex_code ?? '#ccc' }};"
                                            data-color-id="{{ $color->id }}"
                                            title="{{ $color->name }}"
                                            aria-label="{{ $color->name }}"
                                        ></button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($product->storages->count())
                            <div class="variant-group">
                                <span class="variant-label">Storage</span>
                                <div class="variant-storage-options" role="radiogroup" aria-label="Storage">
                                    @foreach($product->storages as $storage)
                                        <button
                                            type="button"
                                            class="variant-storage-option"
                                            data-storage-id="{{ $storage->id }}"
                                        >{{ $storage->label ?? $storage->capacity ?? $storage->name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="variant-error" hidden></div>
                    </div>
                @endif

               {{-- Actions Form --}}
<form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="color_id" id="selected-color-id" value="">
    <input type="hidden" name="phone_storage_id" id="selected-storage-id" value="">
    <input type="hidden" name="buy_now" id="buy-now-flag" value="0">

    <div class="action-area">
        <div class="qty-selector">
            <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
            <input type="number" name="quantity" id="qty-input" class="qty-input" value="1" min="1" max="{{ $stockTotal > 0 ? $stockTotal : 1 }}">
            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
        </div>

        @auth
            @if($product->is_pre_order)
                <button type="submit" class="btn-add-cart" style="background: #8E338A;" onclick="document.getElementById('buy-now-flag').value='0';">
                    <i class="fa-solid fa-box-open"></i> Pre-order Now
                </button>
            @else
                <button type="submit" class="btn-add-cart" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="document.getElementById('buy-now-flag').value='0';">
                    <i class="fa-solid fa-cart-shopping"></i> Add To Cart
                </button>

                {{-- NEW: Buy Now button --}}
                <button type="submit" class="btn-buy-now" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="document.getElementById('buy-now-flag').value='1';">
                    <i class="fa-solid fa-bolt"></i> Buy Now
                </button>
            @endif
        @else
            <a href="{{ route('auth.login') }}" class="btn-add-cart">
                Login to Add to Cart
            </a>
        @endauth

        <button type="button" class="btn-wishlist" title="Add to Wishlist" onclick="document.getElementById('wishlist-form').submit();">
            <i class="fa-regular fa-heart"></i>
        </button>
    </div>
</form>

{{-- Hidden Wishlist Form --}}
<form id="wishlist-form" action="{{ route('wishlist.store') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
</form>

            </div>
        </div>
    </div>

    {{-- ── RELATED PRODUCTS ── (unchanged) --}}
    @if(isset($related) && $related->count() > 0)
    <div class="mb-5" style="margin-left: 2rem; margin-right: 2rem;">
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #ececec !important;">
            <h3 style="font-size: 24px; font-weight: 700; color: #253D4E; margin: 0;">Related Products</h3>
        </div>

        <div class="ecom-products-grid" style="margin-left: 1rem; margin-right: 1rem;">
            @foreach($related->take(4) as $relatedProduct)
                <div class="ecom-deal-card">
                    <!-- Your original related product card code (unchanged) -->
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
@push('scripts')
<script>
    // Handle Main Image Swapping
    function changeMainImage(btn, src) {
        document.getElementById('main-image').src = src;

        let buttons = document.querySelectorAll('.thumb-btn');
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    // Handle Quantity Input
    function updateQty(change) {
        let input = document.getElementById('qty-input');
        let currentVal = parseInt(input.value);
        let maxVal = parseInt(input.getAttribute('max'));
        let minVal = parseInt(input.getAttribute('min'));

        let newVal = currentVal + change;

        if (newVal >= minVal && newVal <= maxVal) {
            input.value = newVal;
        }
    }

    // Handle Color/Storage Variant Selection + gate Add to Cart / Buy Now submission
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.querySelector('.variant-selectors');
        const form = document.getElementById('add-to-cart-form');
        if (!form) return;

        const buyNowInput = document.getElementById('buy-now-flag');

        // Track which button triggered the submit (Add to Cart vs Buy Now)
        // and set the flag BEFORE the submit event runs validation.
        form.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.addEventListener('click', function () {
                if (buyNowInput) {
                    buyNowInput.value = btn.classList.contains('btn-buy-now') ? '1' : '0';
                }
            });
        });

        // No variants on this product — let the form submit freely.
        if (!wrapper) return;

        const colorButtons = wrapper.querySelectorAll('.variant-swatch');
        const storageButtons = wrapper.querySelectorAll('.variant-storage-option');
        const colorInput = document.getElementById('selected-color-id');
        const storageInput = document.getElementById('selected-storage-id');
        const errorEl = wrapper.querySelector('.variant-error');

        function selectButton(buttons, btn, input) {
            buttons.forEach(b => b.classList.remove('is-selected'));
            btn.classList.add('is-selected');
            input.value = btn.dataset.colorId ?? btn.dataset.storageId;
            hideError();
        }

        function showError(message) {
            if (!errorEl) return;
            errorEl.textContent = message;
            errorEl.hidden = false;
        }

        function hideError() {
            if (!errorEl) return;
            errorEl.hidden = true;
        }

        colorButtons.forEach(btn => {
            btn.addEventListener('click', () => selectButton(colorButtons, btn, colorInput));
        });

        storageButtons.forEach(btn => {
            btn.addEventListener('click', () => selectButton(storageButtons, btn, storageInput));
        });

        form.addEventListener('submit', function (e) {
            const needsColor = colorButtons.length > 0 && !colorInput.value;
            const needsStorage = storageButtons.length > 0 && !storageInput.value;

            if (needsColor || needsStorage) {
                e.preventDefault();
                const missing = [needsColor ? 'a color' : null, needsStorage ? 'a storage option' : null]
                    .filter(Boolean)
                    .join(' and ');
                showError(`Please select ${missing} before adding to cart.`);
            }
        });
    });
</script>
@endpush