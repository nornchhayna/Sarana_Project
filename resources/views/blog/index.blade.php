@extends('layouts.app')
@section('title', 'Blog')
@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Blog</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($blogs as $blog)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                @if($blog->image)
                    <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6">
                    <h2 class="font-bold text-gray-800 mb-2">
                        <a href="{{ route('blog.show', $blog->slug) }}" class="hover:text-blue-600">
                            {{ $blog->title }}
                        </a>
                    </h2>
                    @if($blog->excerpt)
                        <p class="text-sm text-gray-600 mb-4">{{ $blog->excerpt }}</p>
                    @endif
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : '' }}</span>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-blue-600 hover:underline">Read More</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-12">
                No blog posts found.
            </div>
        @endforelse
    </div>

    @if($blogs->hasPages())
        <div class="mt-8">
            {{ $blogs->links() }}
        </div>
    @endif
</div>

@endsection
