@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#253D4E]">Blog Posts</h1>
        <a href="{{ route('admin.blogs.create') }}"
           class="bg-[#f0603f] hover:bg-[#d94f30] text-white px-4 py-2 rounded-lg font-medium transition">
            + New Post
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Image</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Published</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($blogs as $blog)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if ($blog->image)
                                <img src="{{ Storage::url($blog->image) }}" alt="{{ $blog->title }}"
                                     class="w-14 h-14 object-cover rounded-lg">
                            @else
                                <div class="w-14 h-14 flex items-center justify-center bg-gray-100 text-gray-400 text-xs rounded-lg">
                                    No image
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-[#253D4E]">{{ $blog->title }}</div>
                            <div class="text-sm text-gray-400">{{ $blog->slug }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($blog->is_published)
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                    Published
                                </span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $blog->published_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.blogs.edit', $blog) }}"
                                   class="text-[#3f6fee] hover:underline text-sm font-medium">Edit</a>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                                      onsubmit="return confirm('Delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No blog posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $blogs->links() }}
    </div>
</div>
@endsection