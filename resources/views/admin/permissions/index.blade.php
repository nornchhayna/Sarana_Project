@extends('layouts.admin')

@section('title', 'Permissions')
@section('subtitle', 'Define the actions roles can be granted')

@section('content')
    <div class="max-w-3xl space-y-6">
        <details class="bg-white rounded-lg border" style="border-color: var(--border);">
            <summary class="px-6 py-4 cursor-pointer font-display font-semibold text-sm select-none">
                + New permission
            </summary>
            <form action="{{ route('admin.permissions.store') }}" method="POST" class="px-6 pb-6 space-y-4 border-t pt-4" style="border-color: var(--border);">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" placeholder="e.g. Delete users"
                               class="w-full rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Slug <span class="font-normal" style="color: var(--ink-soft);">(auto-generated if blank)</span>
                        </label>
                        <input type="text" name="slug" placeholder="e.g. users.delete"
                               class="w-full rounded-md border px-3 py-2 text-sm font-mono" style="border-color: var(--border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Group</label>
                        <input type="text" name="group" placeholder="e.g. users"
                               class="w-full rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <input type="text" name="description"
                               class="w-full rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary text-sm font-medium px-4 py-2 rounded-md">Create permission</button>
                </div>
            </form>
        </details>

        @forelse ($permissions as $group => $groupPermissions)
            <div class="bg-white rounded-lg border overflow-hidden" style="border-color: var(--border);">
                <div class="px-6 py-3 border-b" style="border-color: var(--border); background: var(--muted);">
                    <p class="text-xs font-medium uppercase tracking-wide" style="color: var(--ink-soft);">{{ $group }}</p>
                </div>
                <div class="divide-y" style="border-color: var(--border);">
                    @foreach ($groupPermissions as $permission)
                        <details class="group">
                            <summary class="px-6 py-3 flex items-center justify-between cursor-pointer select-none list-none">
                                <div>
                                    <span class="text-sm font-medium">{{ $permission->name }}</span>
                                    <span class="chip-mono inline-block px-1.5 py-0.5 rounded ml-2">{{ $permission->slug }}</span>
                                </div>
                                <span class="text-xs" style="color: var(--ink-soft);">Edit ▾</span>
                            </summary>
                            <div class="px-6 pb-4">
                                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input type="text" name="name" value="{{ $permission->name }}"
                                               class="rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                                        <input type="text" name="slug" value="{{ $permission->slug }}"
                                               class="rounded-md border px-3 py-2 text-sm font-mono" style="border-color: var(--border);">
                                        <input type="text" name="group" value="{{ $permission->group }}"
                                               class="rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                                        <input type="text" name="description" value="{{ $permission->description }}"
                                               class="rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <button type="submit" class="text-sm font-medium px-3 py-1.5 rounded-md border" style="border-color: var(--border);">
                                            Save
                                        </button>
                                    </div>
                                </form>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="mt-2"
                                      onsubmit="return confirm('Delete this permission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium" style="color: var(--danger);">Delete permission</button>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg border p-10 text-center" style="border-color: var(--border); color: var(--ink-soft);">
                No permissions have been created yet.
            </div>
        @endforelse
    </div>
@endsection