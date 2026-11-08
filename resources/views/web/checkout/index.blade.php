@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="checkout-page">
    <div class="checkout-wrapper">

        {{-- ── LEFT: Shipping form ── --}}
        <div class="checkout-form-col">
            <div class="section-card">
                <h3 class="section-heading">Shipping Information</h3>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    <div class="form-group">
                        <label for="shipping_name">Full Name</label>
                        <input type="text" id="shipping_name" name="shipping_name"
                               value="{{ old('shipping_name', $user->name) }}"
                               class="form-input @error('shipping_name') is-invalid @enderror"
                               placeholder="Your full name">
                        @error('shipping_name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                   
<div class="form-row">
    {{-- Phone Number --}}
    <div class="form-group">
        <label for="shipping_phone">Phone Number</label>
        <input
            type="text"
            id="shipping_phone"
            name="shipping_phone"
            value="{{ old('shipping_phone', $user->phone ?? '') }}"
            class="form-input @error('shipping_phone') is-invalid @enderror"
            placeholder="e.g. 012 345 678"
        >
        @error('shipping_phone')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- City --}}
    <div class="form-group">
        <label for="shipping_city">City</label>
        <input
            type="text"
            id="shipping_city"
            name="shipping_city"
            value="{{ old('shipping_city', $user->city ?? '') }}"
            class="form-input @error('shipping_city') is-invalid @enderror"
            placeholder="e.g. Phnom Penh"
        >
        @error('shipping_city')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Delivery Address --}}
<div class="form-group">
    <label for="shipping_address">Delivery Address</label>
    <textarea
        id="shipping_address"
        name="shipping_address"
        rows="3"
        class="form-input @error('shipping_address') is-invalid @enderror"
        placeholder="Street, house number, village..."
    >{{ old('shipping_address', $user->address ?? '') }}</textarea>
    @error('shipping_address')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

{{-- Province --}}
<div class="form-group">
    <label for="shipping_province">
        Province <span class="optional">(optional)</span>
    </label>
    <input
        type="text"
        id="shipping_province"
        name="shipping_province"
        value="{{ old('shipping_province', $user->province ?? '') }}"
        class="form-input @error('shipping_province') is-invalid @enderror"
        placeholder="e.g. Kandal"
    >
    @error('shipping_province')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>


                    <div class="form-group">
                        <label for="notes">Order Notes <span class="optional">(optional)</span></label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="form-input"
                                  placeholder="Special instructions for your order…">{{ old('notes') }}</textarea>
                    </div>

                    @php
                        $khqrActive = (bool) ($siteSettings['khqr_enabled'] ?? true);
                        $codActive = (bool) ($siteSettings['cod_enabled'] ?? true);
                        if (! $khqrActive && ! $codActive) {
                            $codActive = true;
                        }
                    @endphp

                    {{-- Payment method selection (KHQR and COD) --}}
                    <div class="payment-method-block">
                        <h4 class="payment-heading">Payment Method</h4>
                        
                        @if($khqrActive)
                        {{-- KHQR Option --}}
                        <label class="payment-option {{ $khqrActive ? 'selected' : '' }}" id="khqr-option">
                            <input type="radio" name="payment_method" value="khqr" {{ $khqrActive ? 'checked' : '' }}>
                            <div class="payment-option-inner">
                                <img src="{{ asset('images/bakong-logo.png') }}" alt="Bakong KHQR" class="payment-logo"
                                     onerror="this.style.display='none'">
                                <div>
                                    <span class="payment-name">KHQR / Bakong</span>
                                    <span class="payment-desc">Scan QR code with your banking app</span>
                                </div>
                                <span class="payment-check">✓</span>
                            </div>
                        </label>
                        @endif
                        
                        @if($codActive)
                        {{-- Cash on Delivery Option --}}
                        <label class="payment-option {{ !$khqrActive ? 'selected' : '' }}" id="cod-option">
                            <input type="radio" name="payment_method" value="cod" {{ !$khqrActive ? 'checked' : '' }}>
                            <div class="payment-option-inner">
                                <div class="payment-icon">💵</div>
                                <div>
                                    <span class="payment-name">Cash on Delivery</span>
                                    <span class="payment-desc">{{ $siteSettings['cod_instructions'] ?? 'Pay when your order is delivered' }}</span>
                                </div>
                                <span class="payment-check">✓</span>
                            </div>
                        </label>
                        @endif
                    </div>

                    <button type="submit" class="btn-place-order" id="submit-btn">
                        <span class="btn-text" id="btn-text">Place Order &amp; Get QR Code</span>
                        <span class="btn-loading" style="display:none">Processing…</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── RIGHT: Order summary ── --}}
        <aside class="checkout-summary-col">
            <div class="section-card sticky-summary">
                <h3 class="section-heading">Order Summary</h3>

                <div class="summary-items">
                    @foreach($cart->items as $item)
                    <div class="summary-item">
                        <div class="summary-item-img-wrap">
                                   @php
                                            // Try to get variant image matching the selected color
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
                                            
                                            // Fallback to product thumbnail or first image
                                            $imgPath = $variantImage?->image ?? 
                                                       $item->product->thumbnail ?? 
                                                       ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                                        @endphp

                                        <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="product-thumb"
                                             onerror="this.src='{{ asset('images/default-product.png') }}';">
                                        
                            <span class="summary-item-qty">{{ $item->quantity }}</span>
                        </div>
                        <div class="summary-item-info">
                            <span class="summary-item-name">{{ $item->product->name }}</span>
                            @if($item->color || $item->phoneStorage)
                                <span class="summary-item-variant">
                                    {{ $item->color->name ?? '' }}
                                    @if($item->color && $item->phoneStorage) · @endif
                                    {{ $item->phoneStorage->capacity ?? '' }}
                                </span>
                            @endif
                        </div>
                        <span class="summary-item-price">
                            ${{ number_format($item->unit_price * $item->quantity, 2) }}
                        </span>
                    </div>
                    @endforeach
                </div>

                <div class="summary-divider"></div>

                @php
                    $subtotal = $cart->items->sum(fn($i) => $i->unit_price * $i->quantity);
                    $discount = 0;
                    if ($cart->promotion) {
                        $promo = $cart->promotion;
                        $discount = $promo->type === 'percentage'
                            ? round($subtotal * $promo->value / 100, 2)
                            : $promo->value;
                        if ($promo->max_discount_amount) {
                            $discount = min($discount, $promo->max_discount_amount);
                        }
                    }
                    $shippingActive = (bool) ($siteSettings['shipping_enabled'] ?? true);
                    $flatRate = $shippingActive ? (float) ($siteSettings['flat_shipping_rate'] ?? 0) : 0;
                    $freeThreshold = (float) ($siteSettings['free_shipping_threshold'] ?? 0);
                    $shippingFee = ($freeThreshold > 0 && $subtotal >= $freeThreshold) ? 0 : max(0, $flatRate);
                    $total = max(0, $subtotal - $discount + $shippingFee);
                @endphp

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                @if($cart->promotion)
                <div class="summary-row discount-row">
                    <span>Discount ({{ $cart->promotion->code }})</span>
                    <span>-${{ number_format($discount, 2) }}</span>
                </div>
                @endif

                <div class="summary-row">
                    <span>Shipping</span>
                    @if($shippingFee == 0)
                        <span class="free-tag">Free</span>
                    @else
                        <span>${{ number_format($shippingFee, 2) }}</span>
                    @endif
                </div>
                @if(!empty($siteSettings['shipping_estimated_days']))
                    <div style="font-size: 11px; color: #64748b; margin-top: -6px; margin-bottom: 8px;">
                        Est. delivery: {{ $siteSettings['shipping_estimated_days'] }}
                    </div>
                @endif

                <div class="summary-divider"></div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                @if($item->product->gifts && $item->product->gifts->isNotEmpty())
    <div class="border-t border-gray-200 mt-6 pt-5">

        <div class="text-xl font-bold text-[#2B3D5D] text-left sm:text-right mb-4">
            Free Gifts Included
        </div>

        <div class="flex items-center justify-start sm:justify-end gap-8 flex-wrap">

            {{-- Summary (left) --}}
            <div class="sm:mr-auto">
                <div class="flex items-baseline gap-2 text-xl text-[#2B3D5D]">
                    ({{ $item->product->gifts->count() + 1 }} items)
                    <span class="font-extrabold text-2xl text-[#27AE60]">FREE</span>
                </div>
                <div class="text-[13px] text-gray-500 mt-1.5 max-w-[180px]">
                    Included automatically with this order
                </div>
            </div>

            {{-- Connected image tiles (main product + each gift) --}}
            <div class="flex items-center gap-3">
               

                @foreach($item->product->gifts as $gift)
                    <span class="text-xl font-bold text-[#2B3D5D] shrink-0">+</span>
                    <div class="w-[72px] h-[72px] sm:w-24 sm:h-24 border border-gray-200 rounded-lg flex items-center justify-center overflow-hidden bg-white shrink-0">
                        @if($gift->image)
                            <img src="{{ asset('storage/' . $gift->image) }}"
                                 alt="{{ $gift->name }}"
                                 class="w-full h-full object-contain p-2">
                        @else
                            <div class="text-3xl text-gray-300">🎁</div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Itemized checklist --}}
        <ul class="mt-4 flex flex-col items-start sm:items-end gap-2">
            <li class="flex items-center gap-2 text-sm text-[#2B3D5D] text-left sm:text-right">
                <span><span class="font-bold">This item:</span> {{ $item->product->name }}</span>
                <span class="w-[18px] h-[18px] border border-gray-200 rounded flex items-center justify-center text-[10px] text-[#27AE60] shrink-0">
                    <i class="fa-solid fa-check"></i>
                </span>
            </li>
            @foreach($item->product->gifts as $gift)
                <li class="flex items-center gap-2 text-sm text-[#2B3D5D] text-left sm:text-right">
                    <span>
                        {{ $gift->pivot->quantity > 1 ? $gift->pivot->quantity . 'x ' : '' }}{{ $gift->name }}
                        <span class="text-[#27AE60] font-semibold">— FREE</span>
                    </span>
                    <span class="w-[18px] h-[18px] border border-gray-200 rounded flex items-center justify-center text-[10px] text-[#27AE60] shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </li>
            @endforeach
        </ul>

    </div>
@endif
            </div>

        </aside>

    </div>
</div>

   

<script>
// Payment method selection handling
const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
const submitBtnText = document.getElementById('btn-text');

const checkedOption = document.querySelector('input[name="payment_method"]:checked');
if (checkedOption && checkedOption.value === 'cod' && submitBtnText) {
    submitBtnText.textContent = 'Place Order (Cash on Delivery)';
}

paymentOptions.forEach(option => {
    option.addEventListener('change', function() {
        // Remove selected class from all options
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selected class to clicked option
        this.closest('.payment-option').classList.add('selected');
        
        // Update button text based on payment method
        if (this.value === 'khqr') {
            submitBtnText.textContent = 'Place Order & Get QR Code';
        } else if (this.value === 'cod') {
            submitBtnText.textContent = 'Place Order (Cash on Delivery)';
        }
    });
});

// Show loading state on submit
document.getElementById('checkout-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loading').style.display = 'inline';
});
</script>

<style>
.payment-option {
    display: block;
    cursor: pointer;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
}

.payment-option:hover {
    border-color: #2B3D5D;
    background: #f8f9fa;
}

.payment-option.selected {
    border-color: #2B3D5D;
    background: #f0f4ff;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option-inner {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.payment-logo {
    width: 48px;
    height: 48px;
    object-fit: contain;
}

.payment-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: #f0f0f0;
    border-radius: 8px;
}

.payment-name {
    font-weight: 600;
    color: #2B3D5D;
    display: block;
}

.payment-desc {
    font-size: 0.875rem;
    color: #666;
    display: block;
}

.payment-check {
    margin-left: auto;
    color: #27AE60;
    font-weight: bold;
    font-size: 1.25rem;
    opacity: 0;
}

.payment-option.selected .payment-check {
    opacity: 1;
}
</style>
@endsection