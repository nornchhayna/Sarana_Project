@extends('layouts.app')

@section('title', 'Shop Products')
@section('page-title', 'Shop Products')

@section('content')
<section class="section" style="padding: 20px 0;">
    {{-- CRITICAL: This container keeps the grid from stretching full width --}}
    <div class="container">

        @if(session('success') || request('payment') === 'success')
            <div style="background:#edfaf3;border:1px solid #b3e8cc;border-radius:10px;padding:16px 20px;margin-bottom:20px;color:#1a7a45;font-weight:600;">
                {{ session('success') ?? 'Payment successful! Thank you for your purchase. You can continue shopping below.' }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fff5f5;border:1px solid #f5c6cb;border-radius:10px;padding:16px 20px;margin-bottom:20px;color:#c0392b;font-weight:600;">
                {{ session('error') }}
            </div>
        @endif
        
        {{-- Section Header --}}
        <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #2B3D5D; margin: 0;">Deals of the Day</h2>
        </div>

        {{-- CRITICAL: This creates the 4-column layout --}}
        <div class="ecom-products-grid">
            @foreach($featuredProducts as $product)
                <div class="ecom-deal-card">
                    
                    {{-- Discount Badge --}}
                    @if($product->sale_price && $product->base_price > $product->sale_price)
                        @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                        <span class="ecom-deal-badge">-{{ $discount }}%</span>
                    @endif

                 {{-- Image --}}
                    <div class="ecom-deal-img">
                       <a href="{{ route('products.show', $product->slug) }}">
                            {{-- ADD THIS LINE BACK IN --}}
                            @if($product->thumbnail) 
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}">
                            @else
                                <div style="font-size:60px; color:#D1D5DB;">📦</div>
                            @endif
                        </a>
                    </div>

                   

                    {{-- Product Info --}}
                    <span class="ecom-deal-brand">{{ $product->brand->name ?? 'Brand' }}</span>
                    <a href="{{ route('products.show', $product->id) }}" class="ecom-deal-title">{{ $product->name }}</a>
                    
                    <div class="ecom-deal-rating">
                        <span>({{ $product->reviews_count ?? rand(10,50) }})</span>
                        @php $rating = round($product->avg_rating ?? rand(4,5)); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $rating ? '' : 'star-empty' }}"></i>
                        @endfor
                    </div>
<div class="ecom-deal-desc" title="{{ strip_tags($product->description) }}">
                        {{ strip_tags($product->description) ?? 'No description available for this product.' }}
                    </div>
                    <div class="ecom-deal-price">
                        <span class="ecom-deal-price-current">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</span>
                        @if($product->sale_price && $product->base_price > $product->sale_price)
                            <span class="ecom-deal-price-old">${{ number_format($product->base_price, 2) }}</span>
                        @endif
                    </div>

                  {{-- THE SWAP CONTAINER --}}
                    <div class="ecom-deal-swap-area">
                        
                        {{-- 1. Progress Bar --}}
                        @php
                            $sold = rand(100, 300); 
                            $available = rand(50, 200); 
                            $total = $sold + $available;
                            $percentage = ($sold / $total) * 100;
                        @endphp
                        <div class="ecom-deal-progress-wrap">
                            <div class="ecom-deal-progress-bar">
                                <div class="ecom-deal-progress-fill" style="width: {{ $percentage }}%;">
                                    <div class="ecom-deal-progress-thumb"></div>
                                </div>
                            </div>
                            <div class="ecom-deal-stats">
                                <div>Sold: <span>{{ $sold }}</span></div>
                                <div>Available: <span>{{ $available }}</span></div>
                            </div>
                        </div>

                        {{-- 2. Add to Cart Button --}}
                        <div class="ecom-deal-action-wrapper">
                            <form action="{{ route('cart.add') }}" method="POST" style="margin: 0; padding: 0; width: 100%; height: 100%;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
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
</section>

@endsection