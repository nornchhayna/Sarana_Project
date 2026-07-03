@extends('layouts.app')

@section('title', 'All Categories')
@section('page-title', 'Categories')

@section('content')
<section class="section" style="padding: 20px 0;">
    <div class="container">

        <h2 style="font-size: 22px; font-weight: 700; color: #2B3D5D; margin: 0 0 24px 0;">All Categories</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                   style="display: flex; flex-direction: column; align-items: center; gap: 12px;
                          background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
                          padding: 24px 16px; text-decoration: none; transition: box-shadow 0.2s;"
                   onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)'"
                   onmouseout="this.style.boxShadow='none'">

                    {{-- Image or fallback --}}
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}"
                             alt="{{ $category->name }}"
                             style="width: 64px; height: 64px; object-fit: cover; border-radius: 8px;">
                    @else
                        <div style="width:64px; height:64px; background:#f3f4f6; border-radius:8px;
                                    display:flex; align-items:center; justify-content:center; font-size:28px;">
                            🗂️
                        </div>
                    @endif

                    <div style="text-align: center;">
                        <div style="font-size: 14px; font-weight: 600; color: #2B3D5D;">{{ $category->name }}</div>
                        <div style="font-size: 12px; color: #9ca3af; margin-top: 4px;">
                            {{ $category->products_count ?? 0 }} products
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endsection