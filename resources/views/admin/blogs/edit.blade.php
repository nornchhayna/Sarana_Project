@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#253D4E]">Edit Blog Post</h1>
        <a href="{{ route('admin.blogs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Back to list
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-[#253D4E] mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" required
                       class="w-full bg-gray-100 border-0 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#f0603f] focus:bg-white">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-[#253D4E] mb-1">Excerpt</label>
                <textarea name="excerpt" id="excerpt" rows="2"
                          class="w-full bg-gray-100 border-0 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#f0603f] focus:bg-white">{{ old('excerpt', $blog->excerpt) }}</textarea>
                @error('excerpt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-[#253D4E] mb-1">Content</label>
                <textarea name="content" id="content" rows="10" required
                          class="w-full bg-gray-100 border-0 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#f0603f] focus:bg-white">{{ old('content', $blog->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-[#253D4E] mb-1">Featured Image</label>
                @if ($blog->image)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ Storage::url($blog->image) }}" alt="{{ $blog->title }}"
                             class="w-16 h-16 object-cover rounded-lg">
                        <span class="text-xs text-gray-400">Current image</span>
                    </div>
                @endif
                <input type="file" name="image" id="image" accept="image/*"
                       class="w-full bg-gray-100 border-0 rounded-lg px-4 py-2.5 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#253D4E] file:text-white file:text-xs">
                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-6 items-end">
                <label class="flex items-center gap-2 text-sm text-[#253D4E]">
                    <input type="checkbox" name="is_published" value="1"
                           {{ old('is_published', $blog->is_published) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#f0603f] focus:ring-[#f0603f]">
                    Published
                </label>

                <div class="flex-1">
                    <label for="published_at" class="block text-sm font-medium text-[#253D4E] mb-1">Publish date</label>
                    <input type="date" name="published_at" id="published_at"
                           value="{{ old('published_at', $blog->published_at?->format('Y-m-d')) }}"
                           class="w-full bg-gray-100 border-0 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#f0603f] focus:bg-white">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-[#f0603f] hover:bg-[#d94f30] text-white px-5 py-2.5 rounded-lg font-medium text-sm transition">
                    Update Post
                </button>
                <a href="{{ route('admin.blogs.index') }}"
                   class="px-5 py-2.5 rounded-lg font-medium text-sm text-gray-500 hover:bg-gray-100 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection