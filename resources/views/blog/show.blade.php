@extends('layouts.app')
@section('title', $blog->title)
@section('content')

<div class="max-w-4xl mx-auto px-4 py-8">
    @if($blog->image)
        <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-64 object-cover rounded-xl mb-8">
    @endif

    <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $blog->title }}</h1>

    <div class="flex items-center gap-4 text-sm text-gray-500 mb-8">
        @if($blog->author)
            <span>By {{ $blog->author->name }}</span>
        @endif
        @if($blog->published_at)
            <span>{{ $blog->published_at->format('M d, Y') }}</span>
        @endif
    </div>

    <div class="prose max-w-none text-gray-700">
        {!! $blog->content !!}
    </div>

    <div class="mt-12 pt-8 border-t border-gray-200">
        <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">
            ← Back to Blog
        </a>
    </div>
</div>

@endsection
