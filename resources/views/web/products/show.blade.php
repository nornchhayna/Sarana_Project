@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/productshow.css') }}">
@endpush

@section('content')
<div class="container-xl">

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

    <div class="product-details-wrapper">
        <div class="product-grid">

            {{-- Left: Image Gallery --}}
            <div>
                @php
                    // Get images from product_images table using variantImages relationship
                    $productImages = $product->variantImages()->orderBy('sort_order')->get();
                    $images = [];

                    if ($productImages->count() > 0) {
                        $images = $productImages->map(fn($img) => asset('storage/' . $img->image))->toArray();
                    }

                    // Use thumbnail as main image if no product images exist
                    if (empty($images) && $product->thumbnail) {
                        $images = [asset('storage/' . $product->thumbnail)];
                    }

                    // If still no images, use placeholder
                    if (empty($images)) {
                        $images = [];
                    }
                @endphp

                <div class="gallery-main">
                    @if(count($images) > 0)
                        <img id="main-image" src="{{ $images[0] }}" alt="{{ $product->name }}">
                    @else
                        <div style="font-size: 80px; color: var(--clr-border);">📦</div>
                    @endif
                </div>

                @if(count($images) > 1)
                    <div class="gallery-thumbs">
                        @foreach($images as $index => $img)
                            <button class="thumb-btn {{ $index === 0 ? 'active' : '' }}" onclick="changeMainImage(this, '{{ $img }}')">
                                <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}">
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
        @php $rating = round($averageRating ?? 0); @endphp
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $rating) <i class="fa-solid fa-star"></i>
            @else <i class="fa-regular fa-star"></i>
            @endif
        @endfor
    </div>
    <span class="rating-bar-pct">{{ $reviewCount ?? 0 }} Reviews</span>

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
                        @php $stockTotal = $product->variants ? $product->variants->sum('stock') : 0; @endphp
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

        <button type="button" class="btn-wishlist" title="Add to Wishlist" onclick="submitWishlist();">
            <i class="fa-regular fa-heart"></i>
        </button>
    </div>
</form>

{{-- Hidden Wishlist Form --}}
<form id="wishlist-form" action="{{ route('wishlist.store') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="color_id" id="wishlist-color-id" value="">
    <input type="hidden" name="phone_storage_id" id="wishlist-storage-id" value="">
</form>

            </div>
        </div>
    </div>

{{-- Reviews Section --}}
<section class="reviews-section">

    {{-- Top stats row --}}
    <div class="reviews-stats">
        <div>
            @for ($star = 5; $star >= 1; $star--)
                @php $count = $ratingBreakdown[$star] ?? 0; @endphp
                <div class="rating-bar-row">
                    <span class="rating-bar-star">{{ $star }} ★</span>
                    <div class="rating-bar-track">
                        <div class="rating-bar-fill" style="width: {{ $reviewCount ? ($count / $reviewCount * 100) : 0 }}%"></div>
                    </div>
                    <span class="rating-bar-pct">{{ $reviewCount ? round($count / $reviewCount * 100) : 0 }}%</span>
                </div>
            @endfor
        </div>

        <div>
            <p class="reviews-stat-title">Total Reviews</p>
            <div class="reviews-stat-value">{{ $reviewCount }} <span class="stat-suffix">Reviews</span></div>
        </div>

        <div>
            <p class="reviews-stat-title">Average Rating</p>
            <div class="reviews-stat-rating">
                {{ number_format($averageRating, 1) }}
                <span class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        {{ $i <= round($averageRating) ? '★' : '☆' }}
                    @endfor
                </span>
            </div>
        </div>
    </div>

    {{-- Title + filters --}}
    <div class="reviews-header">
        <h2 class="reviews-title">Customer Feedbacks</h2>
    </div>

    {{-- Cards --}}
    <div class="review-grid">
        @forelse($product->reviews as $review)
            <div class="review-card">
                <div class="review-card-header">
                    <div class="review-avatar">
                        {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="review-user-name">{{ $review->user->name ?? 'Anonymous' }}</span>
                </div>

                <div class="review-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        {{ $i <= $review->rating ? '★' : '☆' }}
                    @endfor
                </div>

                @if($review->title)
                    <p style="font-weight:600; font-size:0.875rem; margin-bottom:0.4rem;">{{ $review->title }}</p>
                @endif

                @if($review->body)
                    <p class="review-body">{{ $review->body }}</p>
                @endif

                @if($review->images && count($review->images))
                    <div class="review-images">
                        @foreach($review->images as $imagePath)
                           <img src="{{ Storage::url($imagePath) }}" class="review-image" onclick="openLightbox('{{ Storage::url($imagePath) }}')">
                        @endforeach
                    </div>
                @endif

                <div class="review-footer">
                    <span>{{ $review->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="review-empty">
                <p>No reviews yet. Be the first to review this product!</p>
            </div>
        @endforelse
    </div>
</section>

@if(isset($related) && $related->count() > 0)
@php $sectionId = 'deals-of-the-day'; @endphp
<section class="py-6 sm:py-8">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-[18px] sm:text-[22px] font-bold text-[#2B3D5D]">
                Deals of the Day
            </h2>

            <div class="flex items-center gap-2 sm:gap-3">
                <button type="button" class="carousel-prev-{{ $sectionId }} w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-[#999aae] flex items-center justify-center text-[#2B3D5D] hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7] transition-colors">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button type="button" class="carousel-next-{{ $sectionId }} w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-[#999aae] flex items-center justify-center text-[#2B3D5D] hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7] transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        <div
            id="carousel-track-{{ $sectionId }}"
            class="carousel-track flex gap-2 sm:gap-3 md:gap-4 lg:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar"
        >
            @foreach($related as $product)
                <div class="carousel-item snap-start shrink-0 w-[calc(50%-4px)] sm:w-[calc(33.333%-8px)] md:w-[calc(25%-12px)] lg:w-[calc(20%-19.2px)]">
                    @include('layouts.partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

    </div>
</section>
@endif
<br>
{{-- Image Lightbox --}}
<div class="image-lightbox" id="image-lightbox" onclick="if(event.target === this) closeLightbox()">
    <button type="button" class="image-lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightbox-img" src="" alt="Review image">
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

    // Submit the wishlist form, but first copy over whatever color/storage
    // is currently selected on the Add to Cart form, so the wishlist entry
    // remembers the same variant instead of saving color_id/phone_storage_id
    // as null.
    function submitWishlist() {
        const selectedColor = document.getElementById('selected-color-id');
        const selectedStorage = document.getElementById('selected-storage-id');
        const wishlistColor = document.getElementById('wishlist-color-id');
        const wishlistStorage = document.getElementById('wishlist-storage-id');

        if (wishlistColor) wishlistColor.value = selectedColor ? selectedColor.value : '';
        if (wishlistStorage) wishlistStorage.value = selectedStorage ? selectedStorage.value : '';

        document.getElementById('wishlist-form').submit();
    }

    // Handle Color/Storage Variant Selection + gate Add to Cart / Buy Now submission
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.querySelector('.variant-selectors');
        const form = document.getElementById('add-to-cart-form');
        if (!form) return;

        const buyNowInput = document.getElementById('buy-now-flag');
        const productId = wrapper?.dataset.productId;

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

        let selectedColorId = null;
        let selectedStorageId = null;

        function selectButton(buttons, btn, input) {
            buttons.forEach(b => b.classList.remove('is-selected'));
            btn.classList.add('is-selected');

            if (btn.dataset.colorId) {
                selectedColorId = btn.dataset.colorId;
                colorInput.value = selectedColorId;
            }
            if (btn.dataset.storageId) {
                selectedStorageId = btn.dataset.storageId;
                storageInput.value = selectedStorageId;
            }
            hideError();

            // Fetch variant data
            fetchVariantData();
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

        async function fetchVariantData() {
            if (!productId) return;

            const params = new URLSearchParams({
                product_id: productId
            });

            if (selectedColorId) params.append('color_id', selectedColorId);
            if (selectedStorageId) params.append('phone_storage_id', selectedStorageId);

            try {
                const response = await fetch(`/api/v1/product-variant?${params}`);
                const data = await response.json();

                if (data.success) {
                    updateVariantUI(data.data);
                } else {
                    console.error('Variant not found:', data.message);
                }
            } catch (error) {
                console.error('Error fetching variant:', error);
            }
        }

        function updateVariantUI(variant) {
            // Update price
            const priceCurrent = document.querySelector('.price-current');
            const priceOld = document.querySelector('.price-old');
            const badgeDiscount = document.querySelector('.badge-discount');

            if (priceCurrent) {
                priceCurrent.textContent = `$${parseFloat(variant.price).toFixed(2)}`;
            }

            // Update SKU
            const skuElement = document.querySelector('.prod-sold strong');
            if (skuElement && variant.sku) {
                // Find the SKU element specifically
                const skuText = Array.from(document.querySelectorAll('.prod-sold'))
                    .find(el => el.textContent.includes('SKU'));
                if (skuText) {
                    const skuStrong = skuText.querySelector('strong');
                    if (skuStrong) skuStrong.textContent = variant.sku;
                }
            }

            // Update stock availability
            const stockElement = document.querySelector('.info-list li:nth-child(2) strong');
            if (stockElement) {
                if (variant.stock > 0) {
                    stockElement.textContent = `In Stock (${variant.stock} Units)`;
                    stockElement.style.color = 'var(--clr-sale)';
                } else {
                    stockElement.textContent = 'Out of Stock';
                    stockElement.style.color = '#E74C3C';
                }
            }

            // Update images for the selected color
            if (variant.images && variant.images.length > 0) {
                updateGallery(variant.images);
            }

            // Update quantity input max
            const qtyInput = document.getElementById('qty-input');
            if (qtyInput && variant.stock > 0) {
                qtyInput.max = variant.stock;
            }
        }

    function updateGallery(images) {
    const mainImage = document.getElementById('main-image');
    const thumbsContainer = document.querySelector('.gallery-thumbs');

    if (!mainImage || !images.length) return;

    mainImage.src = images[0].image;

    if (!thumbsContainer) return;

    if (images.length > 1) {
        // Rebuild thumbnails for this variant's images
        thumbsContainer.innerHTML = images.map((img, i) => `
            <button class="thumb-btn ${i === 0 ? 'active' : ''}" onclick="changeMainImage(this, '${img.image}')">
                <img src="${img.image}" alt="Thumbnail ${i + 1}">
            </button>
        `).join('');
        thumbsContainer.style.display = 'flex';
    } else {
        thumbsContainer.style.display = 'none';
    }
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
    // Review image lightbox
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('image-lightbox').classList.add('is-open');
}

function closeLightbox() {
    document.getElementById('image-lightbox').classList.remove('is-open');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush