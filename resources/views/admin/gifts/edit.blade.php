{{-- resources/views/admin/gifts/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">

    <div class="mb-6">
        <a href="{{ route('admin.gifts.index') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">
            ← Back to Gifts
        </a>
        <h1 class="text-xl font-semibold text-[#253D4E] mt-2">Edit Gift</h1>
        <p class="text-sm text-gray-500 mt-1">Update details for "{{ $gift->name }}".</p>
    </div>

    <form action="{{ route('admin.gifts.update', $gift) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.gifts.partials.form')
    </form>
</div>
@endsection