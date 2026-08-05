@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <div class="flex items-start justify-between gap-4 flex-wrap mb-7">
        <div>
            <h1 class="text-[26px] font-bold text-[#2B3D5D] m-0 mb-1 tracking-tight">My Wishlist</h1>
            <p class="text-sm text-[#9CA3AF] m-0">Items you've saved to buy later</p>
        </div>
        <span class="text-[13px] font-semibold text-[#0056b3] bg-[#EEF4FC] px-3.5 py-1.5 rounded-full whitespace-nowrap">
            {{ $wishlists->total() }} item{{ $wishlists->total() === 1 ? '' : 's' }}
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->isEmpty())
        <div class="text-center py-24 px-5 bg-white border border-[#EEF0F3] rounded-2xl">
            <div class="w-18 h-18 mx-auto mb-4.5 rounded-full bg-[#F7F8FA] flex items-center justify-center" style="width:72px;height:72px;">
                <i class="fa-regular fa-heart text-[28px] text-[#D7DCE3]"></i>
            </div>
            <h3 class="text-lg font-bold text-[#2B3D5D] m-0 mb-1.5">Your wishlist is empty</h3>
            <p class="m-0 mb-5.5 text-[#9CA3AF] text-sm">Nothing saved yet — items you like will show up here.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3 text-[13px] font-semibold rounded-lg border-none bg-[#0056b3] text-white hover:bg-[#004494] no-underline">
                Browse products
            </a>
        </div>
    @else

        {{-- ── DESKTOP / TABLET TABLE ── --}}
        <div class="hidden md:block bg-white border border-[#EEF0F3] rounded-2xl overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-[#EEF0F3]">
                        <th class="w-10 py-4 pl-5"><input type="checkbox" class="rounded border-[#D7DCE3]"></th>
                        <th class="text-left text-xs font-semibold tracking-wide text-[#9CA3AF] uppercase py-4 px-3">Product</th>
                        <th class="text-left text-xs font-semibold tracking-wide text-[#9CA3AF] uppercase py-4 px-3">Quantity</th>
                        <th class="text-left text-xs font-semibold tracking-wide text-[#9CA3AF] uppercase py-4 px-3">Price</th>
                        <th class="text-left text-xs font-semibold tracking-wide text-[#9CA3AF] uppercase py-4 px-3">Stock Status</th>
                        <th class="text-right text-xs font-semibold tracking-wide text-[#9CA3AF] uppercase py-4 pr-5">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wishlists as $item)
                        @php
                            $product = $item->product;
                            if (!$product) continue;
                            $stockTotal = $product->variants ? $product->variants->sum('stock') : 0;
                            $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                            $thumb = $product->thumbnail ?? ($images[0] ?? null);
                            $inStock = $stockTotal > 0;
                        @endphp
                        <tr class="border-b border-[#EEF0F3] last:border-b-0 align-middle" data-wishlist-row>
                            <td class="pl-5 py-4"><input type="checkbox" class="rounded border-[#D7DCE3]"></td>

                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3.5">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="shrink-0 w-14 h-14 rounded-lg bg-[#F7F8FA] flex items-center justify-center overflow-hidden">
                                        @if($thumb)
                                            <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}"
                                                 class="max-w-[80%] max-h-[80%] object-contain {{ $inStock ? '' : 'opacity-50' }}">
                                        @else
                                            <span class="text-2xl text-[#D7DCE3]">📦</span>
                                        @endif
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                           class="block text-sm font-semibold text-[#2B3D5D] hover:text-[#0056b3] no-underline truncate max-w-[220px]">
                                            {{ $product->name }}
                                        </a>
                                        <span class="block text-xs text-[#9CA3AF] mt-0.5">
                                            Added on {{ $item->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-4">
                                <div class="inline-flex items-center border border-[#EEF0F3] rounded-lg overflow-hidden" data-qty-group="{{ $product->id }}">
                                    <button type="button" class="w-7 h-7 flex items-center justify-center text-[#4F5D77] hover:bg-[#F7F8FA]" data-qty-decrease>−</button>
                                    <input type="text" value="1" readonly
                                           class="w-9 h-7 text-center text-sm text-[#2B3D5D] border-x border-[#EEF0F3] focus:outline-none" data-qty-input>
                                    <button type="button" class="w-7 h-7 flex items-center justify-center text-[#4F5D77] hover:bg-[#F7F8FA]" data-qty-increase>+</button>
                                </div>
                            </td>

                            <td class="px-3 py-4">
                                <div class="flex items-center gap-2">
                                    @if($product->sale_price && $product->base_price > $product->sale_price)
                                        <span class="text-xs text-[#9CA3AF] line-through">${{ number_format($product->base_price, 2) }}</span>
                                        <span class="text-sm font-bold text-[#E74C3C]">${{ number_format($product->sale_price, 2) }}</span>
                                    @else
                                        <span class="text-sm font-bold text-[#2B3D5D]">${{ number_format($product->base_price, 2) }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-3 py-4">
                                <span class="inline-flex items-center gap-1.5 text-[13px] font-medium {{ $inStock ? 'text-[#16A34A]' : 'text-[#E74C3C]' }}">
                                    <i class="fa-solid {{ $inStock ? 'fa-check' : 'fa-xmark' }} text-[11px]"></i>
                                    {{ $inStock ? $stockTotal . ' in stock' : 'Out of stock' }}
                                </span>
                            </td>

                            <td class="pr-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-[#EEF0F3] text-[#4F5D77] hover:bg-[#F7F8FA]"
                                       title="View product">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </a>
                                    <form action="{{ route('cart.add') }}" method="POST" class="m-0" data-cart-form="{{ $product->id }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="color_id" value="{{ $item->color_id }}">
                                        <input type="hidden" name="phone_storage_id" value="{{ $item->phone_storage_id }}">
                                        <input type="hidden" name="quantity" value="1" data-cart-qty>
                                        <button type="submit"
                                                class="px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-[#0056b3] hover:bg-[#004494] disabled:bg-[#E5E7EB] disabled:text-[#9CA3AF] disabled:cursor-not-allowed whitespace-nowrap"
                                                {{ $inStock ? '' : 'disabled' }}>
                                            Add To Cart
                                        </button>
                                    </form>
                                    <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 flex items-center justify-center rounded-lg border border-[#EEF0F3] text-[#9CA3AF] hover:text-white hover:bg-[#E74C3C]"
                                                title="Remove from wishlist">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARD LIST ── --}}
        <div class="md:hidden flex flex-col gap-3">
            @foreach($wishlists as $item)
                @php
                    $product = $item->product;
                    if (!$product) continue;
                    $stockTotal = $product->variants ? $product->variants->sum('stock') : 0;
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    $thumb = $product->thumbnail ?? ($images[0] ?? null);
                    $inStock = $stockTotal > 0;
                @endphp
                <div class="bg-white border border-[#EEF0F3] rounded-2xl p-4">
                    <div class="flex gap-3">
                        <input type="checkbox" class="mt-1 rounded border-[#D7DCE3] shrink-0">

                        <a href="{{ route('products.show', $product->slug) }}"
                           class="shrink-0 w-16 h-16 rounded-lg bg-[#F7F8FA] flex items-center justify-center overflow-hidden">
                            @if($thumb)
                                <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}"
                                     class="max-w-[80%] max-h-[80%] object-contain {{ $inStock ? '' : 'opacity-50' }}">
                            @else
                                <span class="text-2xl text-[#D7DCE3]">📦</span>
                            @endif
                        </a>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="text-sm font-semibold text-[#2B3D5D] no-underline leading-snug">
                                    {{ $product->name }}
                                </a>
                                <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="m-0 shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full text-[#9CA3AF] hover:text-white hover:bg-[#E74C3C]" title="Remove">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </form>
                            </div>
                            <span class="block text-xs text-[#9CA3AF] mt-0.5">Added on {{ $item->created_at->format('M d, Y') }}</span>

                            <div class="flex items-center gap-2 mt-2">
                                @if($product->sale_price && $product->base_price > $product->sale_price)
                                    <span class="text-xs text-[#9CA3AF] line-through">${{ number_format($product->base_price, 2) }}</span>
                                    <span class="text-sm font-bold text-[#E74C3C]">${{ number_format($product->sale_price, 2) }}</span>
                                @else
                                    <span class="text-sm font-bold text-[#2B3D5D]">${{ number_format($product->base_price, 2) }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1 text-xs font-medium {{ $inStock ? 'text-[#16A34A]' : 'text-[#E74C3C]' }}">
                                    <i class="fa-solid {{ $inStock ? 'fa-check' : 'fa-xmark' }} text-[10px]"></i>
                                    {{ $inStock ? 'In stock' : 'Out of stock' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 mt-3.5 pt-3.5 border-t border-[#EEF0F3]">
                        <div class="inline-flex items-center border border-[#EEF0F3] rounded-lg overflow-hidden" data-qty-group="{{ $product->id }}">
                            <button type="button" class="w-8 h-8 flex items-center justify-center text-[#4F5D77]" data-qty-decrease>−</button>
                            <input type="text" value="1" readonly class="w-9 h-8 text-center text-sm text-[#2B3D5D] border-x border-[#EEF0F3]" data-qty-input>
                            <button type="button" class="w-8 h-8 flex items-center justify-center text-[#4F5D77]" data-qty-increase>+</button>
                        </div>
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1 m-0" data-cart-form="{{ $product->id }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="color_id" value="{{ $item->color_id }}">
                            <input type="hidden" name="phone_storage_id" value="{{ $item->phone_storage_id }}">
                            <input type="hidden" name="quantity" value="1" data-cart-qty>
                            <button type="submit"
                                    class="w-full px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-[#0056b3] disabled:bg-[#E5E7EB] disabled:text-[#9CA3AF]"
                                    {{ $inStock ? '' : 'disabled' }}>
                                <i class="fa-solid fa-cart-shopping mr-1.5"></i> Add to cart
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

@push('scripts')
<script>
    // Quantity stepper: keeps the visible input and each product's hidden
    // "quantity" field (inside its own Add to Cart form) in sync.
    function syncCartQty(productId, value) {
        document.querySelectorAll('[data-cart-form="' + productId + '"] [data-cart-qty]')
            .forEach(el => el.value = value);
    }

    document.querySelectorAll('[data-qty-group]').forEach(group => {
        const productId = group.dataset.qtyGroup;
        const input = group.querySelector('[data-qty-input]');

        group.querySelector('[data-qty-increase]').addEventListener('click', () => {
            input.value = parseInt(input.value || 1) + 1;
            syncCartQty(productId, input.value);
        });

        group.querySelector('[data-qty-decrease]').addEventListener('click', () => {
            input.value = Math.max(1, parseInt(input.value || 1) - 1);
            syncCartQty(productId, input.value);
        });
    });

    // Submits the Add to Cart form via fetch and reflects the real
    // success/error state returned by the server.
    document.querySelectorAll('[data-cart-form]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = 'Adding…';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            })
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (ok && data.success) {
                        button.innerHTML = 'Added ✓';
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }, 1200);
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        alert(data.message || 'Could not add this item to your cart.');
                    }
                })
                .catch(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    alert('Could not add this item to your cart. Please try again.');
                });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .wl-page, .wl-page * {
        box-sizing: border-box;
    }
</style>
@endpush
@endsection