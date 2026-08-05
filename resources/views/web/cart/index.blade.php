@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="cart-page">
    <div class="cart-wrapper">

        {{-- LEFT: Order Summary Sidebar --}}
        <aside class="cart-sidebar">
            <div class="summary-card">
                <div class="summary-row">
                    <span class="summary-amount">${{ number_format($cart->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}</span>
                    <span class="summary-label">Subtotal</span>
                </div>
                <div class="summary-row">
                    <span class="summary-amount free">Free</span>
                    <span class="summary-label">Shipping</span>
                </div>
                <div class="summary-row estimate-row">
                    <span class="summary-country">United Kingdom</span>
                    <span class="summary-label">Estimate for</span>
                </div>
                <div class="summary-row total-row">
                    <span class="summary-total">${{ number_format($cart->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}</span>
                    <span class="summary-label">Total</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn-checkout">Proceed To CheckOut</a>
                
            </div>
        </aside>

        {{-- RIGHT: Cart Table --}}
        <main class="cart-main">
            @if($cart->items->isEmpty())
                <div class="empty-cart">
                    <p>Your cart is currently empty. <a href="{{ route('products.index') }}">Start shopping</a>.</p>
                </div>
            @else
                <div class="cart-table-wrap">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="col-product">
                                    <label class="select-all-wrap">
                                        <input type="checkbox" id="selectAll"> Product
                                    </label>
                                </th>
                                <th class="col-price">Unit Price</th>
                                <th class="col-qty">Quantity</th>
                                <th class="col-subtotal">Subtotal</th>
                                <th class="col-remove">Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->items as $item)
                            <tr class="cart-row">
                                <td class="col-product">
                                    <div class="product-cell">
                                        <input type="checkbox" class="row-check">
                                        
                                        @php
                                            $imgPath = $item->product->thumbnail ?? 
                                                       ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                                        @endphp

                                        <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="product-thumb"
                                             onerror="this.src='{{ asset('images/default-product.png') }}';">
                                        
                                        <div class="product-info">
                                            <span class="product-name">{{ $item->product->name }}</span>
                                            @if($item->product->reviews_count ?? false)
                                                <span class="product-rating">
                                                    ({{ $item->product->reviews_count }})
                                                    <span class="stars">★★★★★</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="col-price">
                                    <span class="price">${{ number_format($item->unit_price, 2) }}</span>
                                </td>
                                <td class="col-qty">
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="qty-form">
                                        @csrf @method('PUT')
                                        <div class="qty-control">
                                            <button type="button" class="qty-btn qty-minus" onclick="changeQty(this, -1)">−</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="qty-input" onchange="this.form.submit()">
                                            <button type="button" class="qty-btn qty-plus" onclick="changeQty(this, 1)">+</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="col-subtotal">
                                    <span class="price">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                                </td>
                                <td class="col-remove">
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-remove" title="Remove item">
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
                <br>
                <div class="coupon-section">
                        <h5 class="section-title">Apply Coupon</h5>
                        <p class="section-sub">?Using A Promo Code</p>
                        @if($cart->promotion)
                            <div class="promo-applied">
                                <span>Promo: <strong>{{ $cart->promotion->code }}</strong></span>
                                <form action="{{ route('cart.remove-promotion') }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-remove-promo">✕</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.apply-promotion') }}" method="POST" class="coupon-form">
                                @csrf
                                <button type="submit" class="btn-apply">Apply</button>
                                <input type="text" name="code" placeholder="Enter Your Coupon" class="coupon-input">
                            </form>
                        @endif
                        
                    </div>
                <!-- {{-- Bottom Section: Coupon + Shipping --}}
                <div class="cart-bottom">
                
                    {{-- Coupon --}}
                    <div class="coupon-section">
                        <h5 class="section-title">Apply Coupon</h5>
                        <p class="section-sub">?Using A Promo Code</p>
                        @if($cart->promotion)
                            <div class="promo-applied">
                                <span>Promo: <strong>{{ $cart->promotion->code }}</strong></span>
                                <form action="{{ route('cart.remove-promotion') }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-remove-promo">✕</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.apply-promotion') }}" method="POST" class="coupon-form">
                                @csrf
                                <button type="submit" class="btn-apply">Apply</button>
                                <input type="text" name="code" placeholder="Enter Your Coupon" class="coupon-input">
                            </form>
                        @endif
                    </div>

                    {{-- Shipping Calculator --}}
                  
                        <h5 class="section-title">Calculate Shipping</h5>
                        <p class="section-sub">5%: Flat rate</p>
                        <div class="shipping-fields">
                            <select class="shipping-select">
                                <option value="US">USA</option>
                                <option value="GB">United Kingdom</option>
                                <option value="KH">Cambodia</option>
                            </select>
                        </div>
                        <div class="shipping-fields shipping-fields--two">
                            <input type="text" placeholder="PostCode / ZIP" class="shipping-input">
                            <input type="text" placeholder="State / Country" class="shipping-input">
                        </div>
                    </div> 
                </div>
                {{-- Cart Actions --}}
                 -->

            @endif
        </main>
    </div>


</div>
@endsection


@push('scripts')
<script>
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