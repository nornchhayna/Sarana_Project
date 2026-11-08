@extends('layouts.admin')

@section('title', 'Edit role')
@section('subtitle', $role->name)

@section('content')
    <form action="{{ route('admin.roles.update', $role) }}" method="POST"
          class="bg-white rounded-lg border max-w-2xl" style="border-color: var(--border);">
        @csrf
        @method('PUT')

        @include('admin.roles.partials.form', ['role' => $role, 'permissions' => $permissions])

        <div class="px-6 py-4 border-t flex justify-end gap-3" style="border-color: var(--border);">
            <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium px-4 py-2 rounded-md border" style="border-color: var(--border);">
                Cancel
            </a>
            <button type="submit" class="btn-primary text-sm font-medium px-4 py-2 rounded-md">
                Save changes
            </button>
        </div>
    </form>
@endsection