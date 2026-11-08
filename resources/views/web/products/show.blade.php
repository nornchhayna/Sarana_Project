@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/productshow.css') }}">
@endpush

@section('content')
<div class="container-xl px-4 sm:px-6">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-custom">
        <a href="{{ route('home') }}">Home</a>
        <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('products.index') }}">Products</a>
        @if($product->category)
            <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif
        <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span class="current">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    <div class="pdp-card p-4 sm:p-6 lg:p-10 mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            {{-- ══════════ LEFT: GALLERY (sticky on desktop) ══════════ --}}
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-6">
                    @php
                        $productImages = $product->variantImages()->orderBy('sort_order')->get();
                        $images = [];

                        if ($productImages->count() > 0) {
                            $images = $productImages->map(fn($img) => asset('storage/' . $img->image))->toArray();
                        }
                        if (empty($images) && $product->thumbnail) {
                            $images = [asset('storage/' . $product->thumbnail)];
                        }
                        if (empty($images)) {
                            $images = [];
                        }
                    @endphp

                    <div class="gallery-main">
                        @if(count($images) > 0)
                            <img id="main-image" src="{{ $images[0] }}" alt="{{ $product->name }}">
                        @else
                            <div style="font-size: 72px; color: var(--line);">📦</div>
                        @endif
                    </div>

        @if(count($images) > 1)
                    
            <div class="gallery-thumbs no-scrollbar mt-3 flex flex-row flex-nowrap items-start overflow-x-auto">
                @foreach($images as $index => $img)
                    <button class="thumb-btn shrink-0 {{ $index === 0 ? 'active' : '' }}"
                    onclick="changeMainImage(this, '{{ $img }}')">
                    <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>
        @endif
        </div>
        </div>
        {{-- ══════════ RIGHT: DETAILS ══════════ --}}
            <div class="lg:col-span-7">
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($product->is_featured)
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-white bg-[var(--accent)] px-3 py-1 rounded-full">⭐ FEATURED</span>
                    @endif
                    @if($product->is_pre_order)
                        <span class="inline-flex items-center text-[11px] font-bold text-white bg-[#8E338A] px-3 py-1 rounded-full">PRE-ORDER</span>
                    @endif
                    @if($product->is_new)
                        <span class="inline-flex items-center text-[11px] font-bold text-white bg-[var(--success)] px-3 py-1 rounded-full">NEW</span>
                    @endif
                </div>
                
                {{-- Brand / Title --}}
                <div class="prod-brand mb-1">{{ $product->brand->name ?? 'UNKNOWN BRAND' }}</div>
                <h1 class="prod-title text-2xl sm:text-3xl mb-3">{{ $product->name }}</h1>

                {{-- Rating & sold --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-[color:var(--muted)] pb-4 mb-5 border-b border-[color:var(--line)]">
                    <div class="stars-row flex items-center gap-0.5 text-sm">
                        @php $rating = round($averageRating ?? 0); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rating) <i class="fa-solid fa-star"></i>
                            @else <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span>{{ $reviewCount ?? 0 }} Reviews</span>
                    <span class="text-[color:var(--line)]">|</span>
                    <span><strong class="text-[color:var(--ink)]">{{ number_format($product->total_sold ?? 0) }}</strong> Sold</span>
                </div>

                {{-- Signature spec strip --}}
                @php $stockTotal = $product->total_stock; @endphp
                <div class="spec-strip no-scrollbar mb-6">
                    @if($product->phoneModel)
                        <div class="spec-chip">
                            <span class="spec-label">Model</span>
                            <span class="spec-value">{{ $product->phoneModel->name }}</span>
                        </div>
                    @endif

                    <div class="spec-chip {{ $stockTotal > 0 ? 'is-live' : 'is-out' }}" id="stock-chip">
                        <span class="spec-label">Stock</span>
                        <span class="spec-value" id="stock-status">{{ $stockTotal > 0 ? 'In stock · '.$stockTotal : 'Out of stock' }}</span>
                    </div>

                    @if($product->sku)
                        <div class="spec-chip">
                            <span class="spec-label">SKU</span>
                            <span class="spec-value" id="sku-value">{{ $product->sku }}</span>
                        </div>
                    @endif

                    @if($product->data_from)
                        <div class="spec-chip">
                            <span class="spec-label">Available From</span>
                            <span class="spec-value">{{ \Carbon\Carbon::parse($product->data_from)->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if($product->token)
                        <div class="spec-chip">
                            <span class="spec-label">Token</span>
                            <span class="spec-value">{{ $product->token }}</span>
                        </div>
                    @endif
                </div>
                {{-- Price --}}
                @php
                    $hasDiscount = $product->sale_price
                        && $product->base_price > 0
                        && $product->sale_price < $product->base_price;
                @endphp
                <div class="flex items-center flex-wrap gap-3 mb-6">
                    <div class="price-current text-3xl sm:text-4xl">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</div>
                    @if($hasDiscount)
                        <div class="price-old text-lg">${{ number_format($product->base_price, 2) }}</div>
                        @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                        <div class="badge-discount text-xs px-2.5 py-1 rounded-md">-{{ $discount }}% OFF</div>
                    @endif
                </div>
                {{-- Variant Selectors --}}
                @php
                    $variantColorIds = $product->variants->where('is_active', true)
                        ->pluck('color_id')
                        ->unique()
                        ->filter()
                        ->values();
                    $stockColorIds = $product->stock
                        ->pluck('color_id')
                        ->unique()
                        ->filter()
                        ->values();
                    $allColorIds = $variantColorIds->merge($stockColorIds)->unique()->filter()->values();

                    if ($allColorIds->isNotEmpty()) {
                        $colorsList = \App\Models\Color::whereIn('id', $allColorIds)->get();
                        if ($product->colors->isNotEmpty()) {
                            $colorsList = $colorsList->merge($product->colors)->unique('id');
                        }
                    } else {
                        $colorsList = $product->colors;
                    }
                @endphp

                @if($colorsList->isNotEmpty() || $product->storages->count() || $product->rams->count() || $product->variants->count())
                    <div class="flex flex-col gap-4 mb-2" data-product-id="{{ $product->id }}" id="variant-selectors-root">

                        @if($colorsList->isNotEmpty())
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="variant-label min-w-[60px]">Color: <span id="selected-color-name" class="font-semibold text-[color:var(--ink)] normal-case"></span></span>
                                <div class="flex gap-2 flex-wrap items-center" role="radiogroup" aria-label="Color">
                                    @foreach($colorsList as $color)
                                        @php
                                            // Get color-specific images
                                            $colorImages = [];
                                            $colorImages = $product->variantImages()
                                                ->where('color_id', $color->id)
                                                ->orderBy('sort_order')
                                                ->pluck('image')
                                                ->map(fn($img) => asset('storage/' . $img))
                                                ->toArray();
                                            if (empty($colorImages)) {
                                                $colorImages = $images;
                                            }
                                        @endphp
                                        <button type="button" class="variant-swatch flex items-center justify-center transition-all"
                                                style="background-color: {{ $color->hex_code ?: '#e2e8f0' }};"
                                                data-color-id="{{ $color->id }}"
                                                data-color-name="{{ $color->name }}"
                                                data-color-images='@json($colorImages)'
                                                title="{{ $color->name }}" aria-label="{{ $color->name }}">
                                            @if(!$color->hex_code)
                                                <span class="text-[10px] font-bold text-gray-700">{{ substr($color->name, 0, 2) }}</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Storage Options --}}
                        @php
                            $uniqueStorages = $product->variants->where('is_active', true)
                                ->pluck('phone_storage_id')
                                ->unique()
                                ->filter()
                                ->values();
                            $storagesList = $uniqueStorages->isNotEmpty()
                                ? \App\Models\PhoneStorage::whereIn('id', $uniqueStorages)->get()
                                : $product->storages;
                        @endphp
                        @if($storagesList->isNotEmpty())
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="variant-label min-w-[60px]">Storage</span>
                                <div class="flex gap-2 flex-wrap" role="radiogroup" aria-label="Storage">
                                    @foreach($storagesList as $storage)
                                        <button type="button" class="variant-option"
                                                data-storage-id="{{ $storage->id }}">
                                            {{ $storage->capacity }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- RAM Options --}}
                        @php
                            $uniqueRams = $product->variants->where('is_active', true)
                                ->pluck('phone_ram_id')
                                ->unique()
                                ->filter()
                                ->values();
                            $ramsList = $uniqueRams->isNotEmpty()
                                ? \App\Models\PhoneRam::whereIn('id', $uniqueRams)->get()
                                : $product->rams;
                        @endphp
                        @if($ramsList->isNotEmpty())
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="variant-label min-w-[60px]">RAM</span>
                                <div class="flex gap-2 flex-wrap" role="radiogroup" aria-label="RAM">
                                    @foreach($ramsList as $ram)
                                        <button type="button" class="variant-option"
                                                data-ram-id="{{ $ram->id }}">
                                            {{ $ram->capacity }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="variant-error" hidden></div>
                    </div>
                @endif
                {{-- Free Gifts --}}
                @if($product->gifts && $product->gifts->isNotEmpty())
                    <div class="free-gifts-box p-5 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-base font-bold text-[color:var(--ink)]">🎁 Free Gifts Included</span>
                            <span class="text-sm text-[color:var(--ink-soft)]">
                                ({{ $product->gifts->count() + 1 }} items) <span class="font-bold text-[color:var(--success)]">FREE</span>
                            </span>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap mb-4">
                            <div class="w-16 h-16 border border-[color:var(--line)] rounded-lg flex items-center justify-center overflow-hidden bg-white shrink-0">
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-1.5">
                                @else
                                    <div class="text-2xl text-[color:var(--muted)]">📦</div>
                                @endif
                            </div>
                            @foreach($product->gifts as $gift)
                                <span class="text-lg font-bold text-[color:var(--muted)] shrink-0">+</span>
                                <div class="w-16 h-16 border border-[color:var(--line)] rounded-lg flex items-center justify-center overflow-hidden bg-white shrink-0">
                                    @if($gift->image)
                                        <img src="{{ asset('storage/' . $gift->image) }}" alt="{{ $gift->name }}" class="w-full h-full object-contain p-1.5">
                                    @else
                                        <div class="text-2xl text-[color:var(--muted)]">🎁</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <ul class="space-y-1.5 text-sm border-t border-[#F3C9AE] pt-3">
                            <li class="flex items-center justify-between gap-3">
                                <span class="text-[color:var(--ink)]"><span class="font-semibold">This item:</span> {{ $product->name }}</span>
                                <i class="fa-solid fa-check text-[color:var(--success)] shrink-0"></i>
                            </li>
                            @foreach($product->gifts as $gift)
                                <li class="flex items-center justify-between gap-3">
                                    <span class="text-[color:var(--ink)]">
                                        {{ $gift->pivot->quantity > 1 ? $gift->pivot->quantity . 'x ' : '' }}{{ $gift->name }}
                                        <span class="text-[color:var(--success)] font-medium">— FREE</span>
                                    </span>
                                    <i class="fa-solid fa-check text-[color:var(--success)] shrink-0"></i>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Overview / Reviews tabbed card --}}
                @php
                    $descLines = collect(preg_split('/\r\n|\r|\n/', (string) $product->description))
                        ->map(fn($line) => trim($line))
                        ->filter(fn($line) => $line !== '')
                        ->values();
                    $overviewSubtitle = $descLines->first();
                    $overviewBullets = $descLines->slice(1);
                @endphp

                <div class="pdp-card p-6 sm:p-8 mb-6" style="box-shadow:none;">
                    <div class="flex items-center justify-center gap-2 mb-6" id="product-tabs">
                        <button type="button" id="tab-overview" onclick="setProductTab('overview')"
                                class="tab-btn px-6 py-2 rounded-full text-sm bg-[color:var(--ink)] text-white transition">
                            Overview
                        </button>
                        <button type="button" id="tab-reviews" onclick="setProductTab('reviews')"
                                class="tab-btn px-6 py-2 rounded-full text-sm text-[color:var(--ink-soft)] hover:bg-[color:var(--paper)] transition">
                            Reviews
                        </button>
                    </div>

                    <div id="panel-overview">
                        <h2 class="text-xl sm:text-2xl font-bold text-[color:var(--ink)] mb-3" style="font-family:var(--font-display);">{{ $product->name }}</h2>

                        @if($overviewSubtitle)
                            <p class="text-[color:var(--ink-soft)] mb-4">{{ $overviewSubtitle }}</p>
                        @endif

                        @if($overviewBullets->isNotEmpty())
                            <ul class="list-disc pl-5 space-y-2 text-[color:var(--ink-soft)] mb-6">
                                @foreach($overviewBullets as $bullet)
                                    <li>{{ $bullet }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-[color:var(--ink-soft)] pt-4 border-t border-[color:var(--line)]">
                            <span>✅ <span class="font-medium text-[color:var(--ink)]">{{ __('messages.warranty') }}:</span> {{ $product->warranty ?? '1 month' }}</span>
                            <span class="text-[color:var(--line)]">|</span>
                            <span>🚚 <span class="font-medium text-[color:var(--ink)]">{{ __('messages.delivery') }}:</span> {{ config('shop.delivery_note', 'Nationwide') }}</span>
                            <span class="text-[color:var(--line)]">|</span>
                            <span><i class="fa-solid fa-phone text-[color:var(--muted)]"></i> <span class="font-medium text-[color:var(--ink)]">{{ __('messages.contact') }}:</span> {{ config('shop.name', config('app.name')) }}</span>
                        </div>

                        {{-- Telegram Contact Button --}}
                        @if(config('shop.telegram'))
                        <div class="mt-6">
                            <a href="https://t.me/{{ ltrim(config('shop.telegram'), '@') }}?text=Hi, I'm interested in {{ $product->name }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-[#0088cc] hover:bg-[#0077b5] text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                Contact on Telegram
                            </a>
                        </div>
                        @endif
                    </div>

                    <div id="panel-reviews" class="hidden text-center py-6">
                        <p class="text-[color:var(--ink-soft)] mb-4">
                            {{ $reviewCount ?? 0 }} reviews · {{ number_format($averageRating ?? 0, 1) }} average rating
                        </p>
                        <a href="#reviews-section" onclick="setProductTab('overview')"
                           class="inline-flex items-center gap-2 text-sm font-medium text-[color:var(--accent)] hover:text-[color:var(--accent-hover)]">
                            View all reviews <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>
                </div>

             

                {{-- Actions Form (desktop / tablet inline bar) --}}
                <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form" class="hidden sm:block">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_variant_id" id="selected-variant-id" value="">
                    <input type="hidden" name="color_id" id="selected-color-id" value="">
                    <input type="hidden" name="phone_storage_id" id="selected-storage-id" value="">
                    <input type="hidden" name="phone_ram_id" id="selected-ram-id" value="">
                    <input type="hidden" name="buy_now" id="buy-now-flag" value="0">

                    <div class="flex items-center gap-3 mt-8 pt-8 border-t border-[color:var(--line)]">
                        <div class="qty-selector">
                            <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                            <input type="number" name="quantity" id="qty-input" class="qty-input" value="1" min="1" max="{{ $stockTotal > 0 ? $stockTotal : 1 }}">
                            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                        </div>

                        @auth
                            @if($product->is_pre_order)
                                <button type="submit" class="btn-add-cart" style="background:#8E338A;" onclick="document.getElementById('buy-now-flag').value='0';">
                                    <i class="fa-solid fa-box-open"></i> Pre-order Now
                                </button>
                            @else
                                <button type="submit" class="btn-add-cart" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="document.getElementById('buy-now-flag').value='0';">
                                    <i class="fa-solid fa-cart-shopping"></i> Add To Cart
                                </button>
                                <button type="submit" class="btn-buy-now" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="document.getElementById('buy-now-flag').value='1';">
                                    <i class="fa-solid fa-bolt"></i> Buy Now
                                </button>
                            @endif
                        @else
                            <a href="{{ route('auth.login') }}" class="btn-add-cart">Login to Add to Cart</a>
                        @endauth

                        <button type="button" class="btn-wishlist" title="Add to Wishlist" onclick="submitWishlist();">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                </form>

                {{-- Hidden Wishlist Form --}}
                <form id="wishlist-form" action="{{ route('wishlist.store') }}" method="POST" class="d-none hidden">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="color_id" id="wishlist-color-id" value="">
                    <input type="hidden" name="phone_storage_id" id="wishlist-storage-id" value="">
                </form>

            </div>
        </div>
    </div>

    {{-- ══════════ REVIEWS ══════════ --}}
    <section class="reviews-section" id="reviews-section">

      

        <div class="flex flex-wrap items-center justify-between gap-4 mb-7">
            <h2 class="reviews-title">Customer Feedbacks</h2>
        </div>

        <div class="review-grid">
            @forelse($product->reviews as $review)
                <div class="review-card">
                    <div class="review-card-header">
                        <div class="review-avatar">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</div>
                        <span class="review-user-name">{{ $review->user->name ?? 'Anonymous' }}</span>
                    </div>

                    <div class="review-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            {{ $i <= $review->rating ? '★' : '☆' }}
                        @endfor
                    </div>

                    @if($review->title)
                        <p class="text-sm font-semibold mb-1.5 text-[color:var(--ink)]">{{ $review->title }}</p>
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
        <section class="py-8">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg sm:text-xl font-bold text-[color:var(--ink)]" style="font-family:var(--font-display);">
                    {{ __('messages.related_products') }}
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" class="carousel-prev-{{ $sectionId }} w-9 h-9 rounded-full border border-[color:var(--line)] flex items-center justify-center text-[color:var(--ink)] hover:bg-[color:var(--ink)] hover:text-white hover:border-[color:var(--ink)] transition-colors">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="carousel-next-{{ $sectionId }} w-9 h-9 rounded-full border border-[color:var(--line)] flex items-center justify-center text-[color:var(--ink)] hover:bg-[color:var(--ink)] hover:text-white hover:border-[color:var(--ink)] transition-colors">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <div id="carousel-track-{{ $sectionId }}" class="carousel-track flex gap-3 sm:gap-4 lg:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar">
                @foreach($related as $relatedProduct)
                    <div class="carousel-item snap-start shrink-0 w-[calc(50%-6px)] sm:w-[calc(33.333%-11px)] md:w-[calc(25%-14px)] lg:w-[calc(20%-19.2px)]">
                        @include('layouts.partials.product-card', ['product' => $relatedProduct])
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- ══════════ MOBILE STICKY BUY BAR ══════════ --}}
<div class="mobile-buy-bar sm:hidden">
    <div class="flex flex-col leading-tight mr-1">
        <span class="mbb-price">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</span>
        @if($hasDiscount)
            <span class="mbb-old">${{ number_format($product->base_price, 2) }}</span>
        @endif
    </div>

@auth
    @if($product->is_pre_order)
        <button type="button" class="btn-add-cart" style="background:#8E338A;" onclick="triggerFormSubmit('0')">
            <i class="fa-solid fa-box-open"></i> <span>Pre-order</span>
        </button>
    @else
        <button type="button" class="btn-add-cart" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="triggerFormSubmit('0')">
            <i class="fa-solid fa-cart-shopping"></i> <span>Add</span>
        </button>
        <button type="button" class="btn-buy-now" {{ $stockTotal <= 0 ? 'disabled' : '' }} onclick="triggerFormSubmit('1')">
            <i class="fa-solid fa-bolt"></i> <span>Buy Now</span>
        </button>
    @endif
    <button type="button" class="btn-wishlist" title="Add to Wishlist" onclick="submitWishlist();">
        <i class="fa-regular fa-heart"></i>
    </button>
@else
    <a href="{{ route('auth.login') }}" class="btn-add-cart">Login to buy</a>
@endauth
</div>

{{-- Image Lightbox --}}
<div class="image-lightbox" id="image-lightbox" onclick="if(event.target === this) closeLightbox()">
    <button type="button" class="image-lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightbox-img" src="" alt="Review image">
</div>

@endsection

@push('scripts')
<script>
    // Overview / Reviews tab toggle
    function setProductTab(tab) {
        const overviewBtn = document.getElementById('tab-overview');
        const reviewsBtn = document.getElementById('tab-reviews');
        const overviewPanel = document.getElementById('panel-overview');
        const reviewsPanel = document.getElementById('panel-reviews');

        const activeClasses = ['bg-[color:var(--ink)]', 'text-white'];
        const inactiveClasses = ['text-[color:var(--ink-soft)]', 'hover:bg-[color:var(--paper)]'];

        if (tab === 'reviews') {
            overviewPanel.classList.add('hidden');
            reviewsPanel.classList.remove('hidden');
            reviewsBtn.classList.add(...activeClasses);
            reviewsBtn.classList.remove(...inactiveClasses);
            overviewBtn.classList.remove(...activeClasses);
            overviewBtn.classList.add(...inactiveClasses);
        } else {
            reviewsPanel.classList.add('hidden');
            overviewPanel.classList.remove('hidden');
            overviewBtn.classList.add(...activeClasses);
            overviewBtn.classList.remove(...inactiveClasses);
            reviewsBtn.classList.remove(...activeClasses);
            reviewsBtn.classList.add(...inactiveClasses);
        }
    }

    // Main image swap
    function changeMainImage(btn, src) {
        document.getElementById('main-image').src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    // Quantity stepper
    function updateQty(change) {
        let input = document.getElementById('qty-input');
        let currentVal = parseInt(input.value);
        let maxVal = parseInt(input.getAttribute('max'));
        let minVal = parseInt(input.getAttribute('min'));
        let newVal = currentVal + change;
        if (newVal >= minVal && newVal <= maxVal) input.value = newVal;
    }

    // Mobile buy bar delegates to the real (hidden-on-mobile) form so all
    // variant validation / buy_now flag logic stays in one place.
    function triggerFormSubmit(buyNow) {
        const form = document.getElementById('add-to-cart-form');
        if (!form) return;
        document.getElementById('buy-now-flag').value = buyNow;
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }

    // Wishlist: copy over currently selected variant before submitting
    function submitWishlist() {
        const selectedColor = document.getElementById('selected-color-id');
        const selectedStorage = document.getElementById('selected-storage-id');
        const wishlistColor = document.getElementById('wishlist-color-id');
        const wishlistStorage = document.getElementById('wishlist-storage-id');

        if (wishlistColor) wishlistColor.value = selectedColor ? selectedColor.value : '';
        if (wishlistStorage) wishlistStorage.value = selectedStorage ? selectedStorage.value : '';

        document.getElementById('wishlist-form').submit();
    }

    // Color/Storage/RAM variant selection + gate Add to Cart / Buy Now submission
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('variant-selectors-root');
        const form = document.getElementById('add-to-cart-form');
        if (!form) return;

        const buyNowInput = document.getElementById('buy-now-flag');
        const productId = wrapper?.dataset.productId;

        form.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.addEventListener('click', function () {
                if (buyNowInput) {
                    buyNowInput.value = btn.classList.contains('btn-buy-now') ? '1' : '0';
                }
            });
        });

        // Store all variants data for lookup
        @php
            $variantsData = $product->variants->where('is_active', true)->map(function($v) {
                return [
                    'id' => $v->id,
                    'color_id' => $v->color_id,
                    'phone_storage_id' => $v->phone_storage_id,
                    'phone_ram_id' => $v->phone_ram_id,
                    'price' => $v->price,
                    'stock' => $v->stock,
                ];
            })->values()->toArray();
        @endphp
        const variantsData = @json($variantsData);

        if (!wrapper) return;

        const colorButtons = wrapper.querySelectorAll('.variant-swatch');
        const storageButtons = wrapper.querySelectorAll('.variant-option[data-storage-id]');
        const ramButtons = wrapper.querySelectorAll('.variant-option[data-ram-id]');
        const variantInput = document.getElementById('selected-variant-id');
        const colorInput = document.getElementById('selected-color-id');
        const storageInput = document.getElementById('selected-storage-id');
        const ramInput = document.getElementById('selected-ram-id');
        const errorEl = wrapper.querySelector('.variant-error');

        let selectedColorId = null;
        let selectedStorageId = null;
        let selectedRamId = null;
        let selectedVariantId = null;

        // Color swatch click handler
        colorButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                colorButtons.forEach(b => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                selectedColorId = btn.dataset.colorId;
                if (colorInput) colorInput.value = selectedColorId;

                const colorNameEl = document.getElementById('selected-color-name');
                if (colorNameEl && btn.dataset.colorName) {
                    colorNameEl.textContent = btn.dataset.colorName;
                }

                // Update gallery images for this color
                const colorImages = btn.dataset.colorImages;
                if (colorImages) {
                    try {
                        const images = JSON.parse(colorImages);
                        if (images && images.length > 0) {
                            updateGallery(images);
                        }
                    } catch (e) {
                        console.error('Error parsing color images:', e);
                    }
                }

                findMatchingVariant();
            });
        });

        // Storage button click handler
        storageButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                storageButtons.forEach(b => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                selectedStorageId = btn.dataset.storageId;
                if (storageInput) storageInput.value = selectedStorageId;
                findMatchingVariant();
            });
        });

        // RAM button click handler
        ramButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                ramButtons.forEach(b => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                selectedRamId = btn.dataset.ramId;
                if (ramInput) ramInput.value = selectedRamId;
                findMatchingVariant();
            });
        });

        function findMatchingVariant() {
            // If there are no variants, allow form submission without variant selection
            if (variantsData.length === 0) {
                hideError();
                return;
            }

            const variant = variantsData.find(v =>
                // Match color (handle null values)
                (colorButtons.length === 0 || selectedColorId === null || v.color_id == selectedColorId) &&
                // Match storage (handle null values)
                (storageButtons.length === 0 || selectedStorageId === null || v.phone_storage_id == selectedStorageId) &&
                // Match RAM (handle null values)
                (ramButtons.length === 0 || selectedRamId === null || v.phone_ram_id == selectedRamId)
            );

            const priceEl = document.querySelector('.price-current');
            const stockStatusEl = document.getElementById('stock-status');
            const stockChipEl = document.getElementById('stock-chip');
            const qtyInput = document.getElementById('qty-input');
            const addButtons = document.querySelectorAll('.btn-add-cart');
            const buyButtons = document.querySelectorAll('.btn-buy-now');

            if (variant) {
                selectedVariantId = variant.id;
                if (variantInput) variantInput.value = selectedVariantId;

                // Update price display
                if (priceEl && variant.price) {
                    priceEl.textContent = '$' + parseFloat(variant.price).toFixed(2);
                }

                // Update stock display
                if (stockStatusEl && stockChipEl) {
                    if (variant.stock > 0) {
                        stockStatusEl.textContent = 'In stock · ' + variant.stock;
                        stockChipEl.classList.remove('is-out');
                        stockChipEl.classList.add('is-live');

                        // Update quantity input max
                        if (qtyInput) {
                            qtyInput.setAttribute('max', variant.stock);
                            if (parseInt(qtyInput.value) > variant.stock) {
                                qtyInput.value = variant.stock;
                            }
                            if (parseInt(qtyInput.value) < 1) qtyInput.value = 1;
                        }
                        addButtons.forEach(b => {
                            b.disabled = false;
                            b.classList.remove('opacity-50', 'cursor-not-allowed');
                        });
                        buyButtons.forEach(b => {
                            b.disabled = false;
                            b.classList.remove('opacity-50', 'cursor-not-allowed');
                        });
                    } else {
                        stockStatusEl.textContent = 'Out of stock';
                        stockChipEl.classList.remove('is-live');
                        stockChipEl.classList.add('is-out');

                        // Disable quantity input
                        if (qtyInput) {
                            qtyInput.setAttribute('max', 0);
                            qtyInput.value = 0;
                        }
                        addButtons.forEach(b => {
                            b.disabled = true;
                            b.classList.add('opacity-50', 'cursor-not-allowed');
                        });
                        buyButtons.forEach(b => {
                            b.disabled = true;
                            b.classList.add('opacity-50', 'cursor-not-allowed');
                        });
                    }
                }

                hideError();
            } else {
                // Combination not found in variants
                selectedVariantId = null;
                if (variantInput) variantInput.value = '';
                if (stockStatusEl && stockChipEl) {
                    stockStatusEl.textContent = 'Unavailable';
                    stockChipEl.classList.remove('is-live');
                    stockChipEl.classList.add('is-out');
                }
                addButtons.forEach(b => {
                    b.disabled = true;
                    b.classList.add('opacity-50', 'cursor-not-allowed');
                });
                buyButtons.forEach(b => {
                    b.disabled = true;
                    b.classList.add('opacity-50', 'cursor-not-allowed');
                });
            }
        }

        // Auto-select preferred variant on page load
        if (variantsData.length > 0) {
            const preferred = variantsData.find(v => v.stock > 0) || variantsData[0];
            if (preferred) {
                if (preferred.color_id) {
                    const btn = wrapper.querySelector(`.variant-swatch[data-color-id="${preferred.color_id}"]`);
                    if (btn) {
                        btn.classList.add('is-selected');
                        selectedColorId = String(preferred.color_id);
                        if (colorInput) colorInput.value = selectedColorId;
                        const colorNameEl = document.getElementById('selected-color-name');
                        if (colorNameEl && btn.dataset.colorName) {
                            colorNameEl.textContent = btn.dataset.colorName;
                        }
                    }
                }
                if (preferred.phone_storage_id) {
                    const btn = wrapper.querySelector(`.variant-option[data-storage-id="${preferred.phone_storage_id}"]`);
                    if (btn) {
                        btn.classList.add('is-selected');
                        selectedStorageId = String(preferred.phone_storage_id);
                        if (storageInput) storageInput.value = selectedStorageId;
                    }
                }
                if (preferred.phone_ram_id) {
                    const btn = wrapper.querySelector(`.variant-option[data-ram-id="${preferred.phone_ram_id}"]`);
                    if (btn) {
                        btn.classList.add('is-selected');
                        selectedRamId = String(preferred.phone_ram_id);
                        if (ramInput) ramInput.value = selectedRamId;
                    }
                }
                findMatchingVariant();
            }
        }

        // Fallback: auto-select first color if not already selected
        if (!selectedColorId && colorButtons.length > 0) {
            const firstColorBtn = colorButtons[0];
            firstColorBtn.classList.add('is-selected');
            selectedColorId = firstColorBtn.dataset.colorId;
            if (colorInput) colorInput.value = selectedColorId;
            const colorNameEl = document.getElementById('selected-color-name');
            if (colorNameEl && firstColorBtn.dataset.colorName) {
                colorNameEl.textContent = firstColorBtn.dataset.colorName;
            }
        }

        function updateGallery(images) {
            const mainImage = document.getElementById('main-image');
            const thumbsContainer = document.querySelector('.gallery-thumbs');
            if (!mainImage || !images.length) return;

            mainImage.src = images[0];
            if (!thumbsContainer) return;

            if (images.length > 1) {
                thumbsContainer.innerHTML = images.map((img, i) => `
                    <button class="thumb-btn shrink-0 ${i === 0 ? 'active' : ''}" onclick="changeMainImage(this, '${img}')">
                        <img src="${img}" alt="Thumbnail ${i + 1}">
                    </button>
                `).join('');
                thumbsContainer.style.display = 'flex';
            } else {
                thumbsContainer.style.display = 'none';
            }
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

        // Form submission validation
        form.addEventListener('submit', function(e) {
            // Allow submission if there are no variants or if a variant was selected
            if (variantsData.length === 0 || selectedVariantId) {
                return;
            }

            // Only validate if there are variant selectors
            if (colorButtons.length === 0 && storageButtons.length === 0 && ramButtons.length === 0) {
                if (variantsData.length > 0) {
                    const firstVariant = variantsData[0];
                    selectedVariantId = firstVariant.id;
                    if (variantInput) variantInput.value = selectedVariantId;
                }
                return;
            }

            // Only validate color if color options exist
            if (colorButtons.length > 0 && !selectedColorId) {
                e.preventDefault();
                showError('Please select a color');
                return;
            }
            // Only validate storage if storage options exist
            if (storageButtons.length > 0 && !selectedStorageId) {
                e.preventDefault();
                showError('Please select a storage option');
                return;
            }
            // Only validate RAM if RAM options exist
            if (ramButtons.length > 0 && !selectedRamId) {
                e.preventDefault();
                showError('Please select a RAM option');
                return;
            }
            // Only validate variant ID if there are multiple variants and no variant was auto-selected
            if (variantsData.length > 1 && !selectedVariantId) {
                e.preventDefault();
                showError('Please select a valid combination');
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