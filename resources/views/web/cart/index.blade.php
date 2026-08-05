@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-[#1562c7] mb-6">Shopping Cart</h1>

    <div class="flex flex-col lg:flex-row-reverse gap-6 lg:gap-8">

        {{-- ORDER SUMMARY (stacks on top on mobile, right column on desktop) --}}
        <aside class="lg:w-80 lg:flex-shrink-0">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 sm:p-6 lg:sticky lg:top-6">
                <h2 class="text-lg font-semibold text-[#253D4E] mb-4">Order Summary</h2>

                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-medium text-[#253D4E]">
                            ${{ number_format($cart->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Shipping</dt>
                        <dd class="font-medium text-green-600">Free</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Estimate for</dt>
                        <dd class="font-medium text-[#253D4E]">Phnom Penh</dd>
                    </div>
                </dl>

                <div class="border-t border-gray-200 mt-4 pt-4 flex items-center justify-between">
                    <span class="text-base font-semibold text-[#253D4E]">Total</span>
                    <span class="text-lg font-bold text-[#1562c7]">
                        ${{ number_format($cart->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}
                    </span>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="mt-5 block w-full text-center rounded-md bg-[#1562c7] hover:bg-[#1562c4] transition-colors text-white font-semibold py-3 px-4">
                    Proceed to Checkout
                </a>
            </div>
        </aside>

        {{-- CART CONTENTS --}}
        <main class="flex-1 min-w-0">
            @if($cart->items->isEmpty())
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-10 text-center">
                    <p class="text-gray-500">
                        Your cart is currently empty.
                        <a href="{{ route('products.index') }}" class="text-[#3f6fee] font-medium hover:underline">Start shopping</a>.
                    </p>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

                    {{-- Desktop / tablet table (md and up) --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr class="text-left text-gray-500 uppercase text-xs tracking-wide">
                                    <th class="py-3 pl-5 pr-3 font-medium">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-[#f0603f] focus:ring-[#f0603f]">
                                            Product
                                        </label>
                                    </th>
                                    <th class="py-3 px-3 font-medium">Color</th>
                                    <th class="py-3 px-3 font-medium">Storage</th>
                                    <th class="py-3 px-3 font-medium">Unit Price</th>
                                    <th class="py-3 px-3 font-medium">Quantity</th>
                                    <th class="py-3 px-3 font-medium">Subtotal</th>
                                    <th class="py-3 pr-5 pl-3 font-medium text-right">Remove</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($cart->items as $item)
                                @php
                                    $variantImage = null;
                                    if ($item->color_id && $item->product->variantImages) {
                                        $variantImage = $item->product->variantImages
                                            ->where('color_id', $item->color_id)
                                            ->where('is_primary', true)
                                            ->first();
                                        if (!$variantImage) {
                                            $variantImage = $item->product->variantImages
                                                ->where('color_id', $item->color_id)
                                                ->first();
                                        }
                                    }
                                    $imgPath = $variantImage?->image ??
                                               $item->product->thumbnail ??
                                               ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                                @endphp
                                <tr class="cart-row hover:bg-gray-50/60">
                                    <td class="py-4 pl-5 pr-3">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" class="row-check rounded border-gray-300 text-[#f0603f] focus:ring-[#f0603f]">
                                            <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-16 h-16 object-cover rounded-md border border-gray-200 flex-shrink-0"
                                                 onerror="this.src='{{ asset('images/default-product.png') }}';">
                                            <div class="min-w-0">
                                                <p class="font-medium text-[#253D4E] truncate">{{ $item->product->name }}</p>
                                                @if($item->product->reviews_count ?? false)
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        ({{ $item->product->reviews_count }})
                                                        <span class="text-amber-400">★★★★★</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-gray-600">{{ $item->color?->name ?? '—' }}</td>
                                    <td class="py-4 px-3 text-gray-600">{{ $item->phoneStorage?->capacity ?? '—' }}</td>
                                    <td class="py-4 px-3 font-medium text-[#253D4E]">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-4 px-3">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="qty-form">
                                            @csrf @method('PUT')
                                            <div class="inline-flex items-center border border-gray-300 rounded-md overflow-hidden">
                                                <button type="button" onclick="changeQty(this, -1)"
                                                        class="qty-btn w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100">−</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                       class="qty-input w-12 h-8 text-center border-x border-gray-300 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                       onchange="this.form.submit()">
                                                <button type="button" onclick="changeQty(this, 1)"
                                                        class="qty-btn w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="py-4 px-3 font-semibold text-[#253D4E]">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                    <td class="py-4 pr-5 pl-3 text-right">
                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Remove item"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14H6L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M9 6V4h6v2"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile stacked cards (below md) --}}
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach($cart->items as $item)
                        @php
                            $variantImage = null;
                            if ($item->color_id && $item->product->variantImages) {
                                $variantImage = $item->product->variantImages
                                    ->where('color_id', $item->color_id)
                                    ->where('is_primary', true)
                                    ->first();
                                if (!$variantImage) {
                                    $variantImage = $item->product->variantImages
                                        ->where('color_id', $item->color_id)
                                        ->first();
                                }
                            }
                            $imgPath = $variantImage?->image ??
                                       $item->product->thumbnail ??
                                       ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                        @endphp
                        <div class="cart-row p-4">
                            <div class="flex gap-3">
                                <input type="checkbox" class="row-check mt-1 rounded border-gray-300 text-[#f0603f] focus:ring-[#f0603f]">
                                <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-16 h-16 object-cover rounded-md border border-gray-200 flex-shrink-0"
                                     onerror="this.src='{{ asset('images/default-product.png') }}';">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="font-medium text-[#253D4E] leading-snug">{{ $item->product->name }}</p>
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="flex-shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Remove item"
                                                    class="inline-flex items-center justify-center w-8 h-8 -mt-1 -mr-1 rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14H6L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M9 6V4h6v2"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($item->color?->name){{ $item->color->name }}@endif
                                        @if($item->color?->name && $item->phoneStorage?->capacity) &middot; @endif
                                        @if($item->phoneStorage?->capacity){{ $item->phoneStorage->capacity }}@endif
                                    </p>

                                    <div class="flex items-center justify-between mt-3">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="qty-form">
                                            @csrf @method('PUT')
                                            <div class="inline-flex items-center border border-gray-300 rounded-md overflow-hidden">
                                                <button type="button" onclick="changeQty(this, -1)"
                                                        class="qty-btn w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100">−</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                       class="qty-input w-12 h-8 text-center border-x border-gray-300 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                       onchange="this.form.submit()">
                                                <button type="button" onclick="changeQty(this, 1)"
                                                        class="qty-btn w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100">+</button>
                                            </div>
                                        </form>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">${{ number_format($item->unit_price, 2) }} each</p>
                                            <p class="font-semibold text-[#253D4E]">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 sm:p-6 mt-6">
                    <h5 class="font-semibold text-[#1562c7]">Apply Coupon</h5>
                    <p class="text-sm text-gray-500 mt-1">Using a promo code?</p>

                    @if($cart->promotion)
                        <div class="mt-3 flex items-center justify-between bg-green-50 border border-green-200 rounded-md px-4 py-3">
                            <span class="text-sm text-green-700">Promo: <strong>{{ $cart->promotion->code }}</strong></span>
                            <form action="{{ route('cart.remove-promotion') }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-green-700 hover:text-green-900 font-bold">✕</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.apply-promotion') }}" method="POST"
                              class="mt-3 flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="text" name="code" placeholder="Enter your coupon"
                                   class="flex-1 rounded-md border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#f0603f]/40 focus:border-[#f0603f]">
                            <button type="submit"
                                    class="rounded-md bg-[#1562c7] hover:bg-[#1562c4] text-white font-semibold px-6 py-2.5 text-sm transition-colors">
                                Apply
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </main>
    </div>
</div>

@if(isset($related) && $related->count() > 0)
@php $sectionId = 'deals-of-the-day'; @endphp
<section class="py-6 sm:py-8">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-[18px] sm:text-[22px] font-bold text-[#2B3D5D]">
                {{ __('messages.related_products') }}
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

@push('scripts')
<script>
(function () {
    const track = document.getElementById('carousel-track-deals-of-the-day');
    if (!track) return;

    const prevBtn = document.querySelector('.carousel-prev-deals-of-the-day');
    const nextBtn = document.querySelector('.carousel-next-deals-of-the-day');

    function cardWidth() {
        const card = track.querySelector(':scope > div');
        if (!card) return 0;
        const gap = window.innerWidth < 640 ? 8 : 24;
        return card.getBoundingClientRect().width + gap;
    }

    function scrollByCards(count) {
        track.scrollBy({ left: cardWidth() * count, behavior: 'smooth' });
    }

    function atEnd() {
        return track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
    }

    prevBtn?.addEventListener('click', () => scrollByCards(-1));
    nextBtn?.addEventListener('click', () => scrollByCards(1));

    setInterval(() => {
        if (atEnd()) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            scrollByCards(1);
        }
    }, 15000);
})();

function changeQty(btn, delta) {
    const form = btn.closest('.qty-form');
    const input = form.querySelector('.qty-input');
    const newVal = Math.max(1, parseInt(input.value) + delta);
    input.value = newVal;
    form.submit();
}

// Select all
const selectAll = document.getElementById('selectAll');
if (selectAll) {
    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    });
}
</script>
@endpush
@endsection