@extends('layouts.app')

@section('title', $category->name)
@section('page-title', $category->name)

@section('content')
<section class="section" style="padding: 20px 0;">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav style="font-size: 13px; color: #9ca3af; margin-bottom: 20px;">
            <a href="{{ route('products.index') }}" style="color:#6b7280; text-decoration:none;">Home</a>
            <span style="margin: 0 6px;">›</span>
            <a href="{{ route('categories.index') }}" style="color:#6b7280; text-decoration:none;">Categories</a>
            @if(isset($parent))
                <span style="margin: 0 6px;">›</span>
                <a href="{{ route('categories.show', $parent->slug) }}" style="color:#6b7280; text-decoration:none;">{{ $parent->name }}</a>
            @endif
            <span style="margin: 0 6px;">›</span>
            <span style="color:#2B3D5D; font-weight:600;">{{ $category->name }}</span>
        </nav>

        <div style="display: flex; gap: 24px; align-items: flex-start;">

            {{-- ===== LEFT SIDEBAR ===== --}}
            <aside style="width: 200px; flex-shrink: 0; background: #fff; border: 1px solid #e5e7eb;
                          border-radius: 12px; padding: 16px; position: sticky; top: 20px;">

                <h3 style="font-size: 14px; font-weight: 700; color: #2B3D5D; margin: 0 0 12px 0;
                            text-transform: uppercase; letter-spacing: 0.05em;">Categories</h3>

                <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px;">

                    <li>
                        <a href="{{ route('products.index') }}"
                           style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px;
                                  font-size:13px; font-weight:600; text-decoration:none; color:#374151;">
                            <i class="fa-solid fa-th-large" style="width:14px; font-size:12px;"></i>
                            All Products
                        </a>
                    </li>

                    @foreach($allCategories as $cat)
                        @php $isActiveParent = $cat->slug === $category->slug || (isset($parent) && $cat->slug === $parent->slug); @endphp
                        <li x-data="{ open: {{ $isActiveParent ? 'true' : 'false' }} }">

                            <div style="display:flex; align-items:center; border-radius:8px; overflow:hidden;">
                                <a href="{{ route('categories.show', $cat->slug) }}"
                                   style="flex:1; display:flex; align-items:center; gap:8px; padding:8px 10px;
                                          font-size:13px; text-decoration:none; transition:background 0.15s;
                                          {{ $isActiveParent ? 'color:#2B3D5D; font-weight:700;' : 'color:#374151; font-weight:500;' }}"
                                   onmouseover="this.style.background='#f3f4f6'"
                                   onmouseout="this.style.background='transparent'">
                                    {{ $cat->name }}
                                    @if($cat->products_count ?? null)
                                        <span style="font-size:11px; color:#9ca3af; margin-left:auto;">{{ $cat->products_count }}</span>
                                    @endif
                                </a>

                                @if($cat->children->isNotEmpty())
                                    <button @click="open = !open"
                                            style="background:none; border:none; cursor:pointer; padding:8px 8px 8px 0; color:#9ca3af;">
                                        <i class="fa-solid fa-chevron-down" style="font-size:10px; transition:transform 0.2s;"
                                           :style="open ? 'transform:rotate(180deg)' : ''"></i>
                                    </button>
                                @endif
                            </div>

                            @if($cat->children->isNotEmpty())
                                <ul x-show="open" x-transition
                                    style="list-style:none; margin:2px 0 4px 0; padding:0 0 0 18px;
                                           display:flex; flex-direction:column; gap:1px; border-left:2px solid #e5e7eb;">
                                    @foreach($cat->children as $child)
                                        @php $isActiveChild = $child->slug === $category->slug; @endphp
                                        <li>
                                            <a href="{{ route('categories.showSubcategory', [$cat->slug, $child->slug]) }}"
                                               style="display:flex; align-items:center; justify-content:space-between;
                                                      padding:6px 10px; border-radius:6px; font-size:12px; text-decoration:none;
                                                      {{ $isActiveChild ? 'background:#EEF2FF; color:#2B3D5D; font-weight:600;' : 'color:#6b7280;' }}"
                                               onmouseover="if(!this.style.background.includes('238')) this.style.background='#f9fafb'"
                                               onmouseout="if(!this.style.background.includes('238')) this.style.background='transparent'">
                                                {{ $child->name }}
                                                @if($child->products_count ?? null)
                                                    <span style="font-size:10px; color:#9ca3af;">{{ $child->products_count }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                        </li>
                    @endforeach
                </ul>

                {{-- Subcategories of current (if any) --}}
                @if($subcategories->isNotEmpty())
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <h4 style="font-size:12px; font-weight:700; color:#9ca3af; text-transform:uppercase; margin:0 0 8px 0;">
                            In {{ $category->name }}
                        </h4>
                        <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:2px;">
                            @foreach($subcategories as $sub)
                                <li>
                                    <a href="{{ route('categories.showSubcategory', [$category->slug, $sub->slug]) }}"
                                       style="display:block; padding:6px 10px; border-radius:6px; font-size:12px;
                                              text-decoration:none; color:#374151;"
                                       onmouseover="this.style.background='#f3f4f6'"
                                       onmouseout="this.style.background='transparent'">
                                        {{ $sub->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </aside>

            {{-- ===== RIGHT: PRODUCTS ===== --}}
            <div style="flex:1; min-width:0;">

                {{-- Sort bar --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                    <h2 style="font-size:20px; font-weight:700; color:#2B3D5D; margin:0;">
                        {{ $category->name }}
                        <span style="font-size:14px; font-weight:400; color:#9ca3af;">({{ $products->total() }} products)</span>
                    </h2>
                    <form method="GET" style="display:flex; gap:8px; align-items:center;">
                        <select name="sort" onchange="this.form.submit()"
                                style="border:1px solid #e5e7eb; border-radius:8px; padding:6px 12px; font-size:13px; color:#374151;">
                            <option value="latest"    {{ $sort === 'latest'     ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ $sort === 'price_low'  ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high"{{ $sort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular"   {{ $sort === 'popular'    ? 'selected' : '' }}>Most Popular</option>
                            <option value="name_asc"  {{ $sort === 'name_asc'   ? 'selected' : '' }}>Name A–Z</option>
                        </select>
                    </form>
                </div>

                {{-- Product grid --}}
                @if($products->isEmpty())
                    <div style="text-align:center; padding:60px 20px; color:#9ca3af;">
                        <div style="font-size:48px; margin-bottom:16px;">📦</div>
                        <p style="font-size:16px;">No products found in this category.</p>
                    </div>
                @else
                    <div class="ecom-products-grid">
                        @foreach($products as $product)
                            <div class="ecom-deal-card">

                                @if($product->sale_price && $product->base_price > $product->sale_price)
                                    @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                                    <span class="ecom-deal-badge">-{{ $discount }}%</span>
                                @endif

                                <div class="ecom-deal-img">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        @if($product->thumbnail)
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}">
                                        @else
                                            <div style="font-size:60px; color:#D1D5DB;">📦</div>
                                        @endif
                                    </a>
                                </div>

                                <span class="ecom-deal-brand">{{ $product->brand->name ?? 'Brand' }}</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="ecom-deal-title">{{ $product->name }}</a>

                                <div class="ecom-deal-price">
                                    <span class="ecom-deal-price-current">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</span>
                                    @if($product->sale_price && $product->base_price > $product->sale_price)
                                        <span class="ecom-deal-price-old">${{ number_format($product->base_price, 2) }}</span>
                                    @endif
                                </div>

                                <div class="ecom-deal-action-wrapper">
                                    <form action="{{ route('cart.add') }}" method="POST" style="margin:0; padding:0; width:100%;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="ecom-deal-btn-cart">
                                            <i class="fa-solid fa-cart-shopping" style="margin-right:8px;"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div style="margin-top: 32px;">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</section>
@endsection