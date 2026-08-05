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
                        <div class="form-group">
                            <label for="shipping_phone">Phone Number</label>
                            <input type="text" id="shipping_phone" name="shipping_phone"
                                   value="{{ old('shipping_phone', $user->phone) }}"
                                   class="form-input @error('shipping_phone') is-invalid @enderror"
                                   placeholder="e.g. 012 345 678">
                            @error('shipping_phone')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_city">City</label>
                            <input type="text" id="shipping_city" name="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   class="form-input @error('shipping_city') is-invalid @enderror"
                                   placeholder="e.g. Phnom Penh">
                            @error('shipping_city')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address">Delivery Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3"
                                  class="form-input @error('shipping_address') is-invalid @enderror"
                                  placeholder="Street, house number, village…">{{ old('shipping_address', $user->address) }}</textarea>
                        @error('shipping_address')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shipping_province">Province <span class="optional">(optional)</span></label>
                        <input type="text" id="shipping_province" name="shipping_province"
                               value="{{ old('shipping_province') }}"
                               class="form-input"
                               placeholder="e.g. Kandal">
                    </div>

                    <div class="form-group">
                        <label for="notes">Order Notes <span class="optional">(optional)</span></label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="form-input"
                                  placeholder="Special instructions for your order…">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Payment method display (only KHQR) --}}
                    <div class="payment-method-block">
                        <h4 class="payment-heading">Payment Method</h4>
                        <label class="payment-option selected">
                            <input type="radio" name="payment_method" value="khqr" checked hidden>
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
                    </div>

                    <button type="submit" class="btn-place-order" id="submit-btn">
                        <span class="btn-text">Place Order &amp; Get QR Code</span>
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
                                            $imgPath = $item->product->thumbnail ?? 
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
                            @if($item->color || $item->storage)
                                <span class="summary-item-variant">
                                    {{ $item->color->name ?? '' }}
                                    @if($item->color && $item->storage) · @endif
                                    {{ $item->storage->capacity ?? '' }}
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
                    $total = max(0, $subtotal - $discount);
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
                    <span class="free-tag">Free</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </aside>

    </div>
</div>

   

<script>
// Show loading state on submit
document.getElementById('checkout-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loading').style.display = 'inline';
});
</script>
@endsection