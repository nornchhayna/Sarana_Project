@php
    $role = $role ?? null;
    $checkedPermissionIds = old('permissions', $role?->permissions->pluck('id')->toArray() ?? []);
@endphp

<div class="p-6 space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="name" class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name ?? '') }}"
                   class="w-full rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium mb-1">
                Slug @if (! $role) <span class="font-normal" style="color: var(--ink-soft);">(auto-generated if left blank)</span> @endif
            </label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $role->slug ?? '') }}"
                   class="w-full rounded-md border px-3 py-2 text-sm font-mono" style="border-color: var(--border);">
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" id="description" rows="2"
                  class="w-full rounded-md border px-3 py-2 text-sm" style="border-color: var(--border);">{{ old('description', $role->description ?? '') }}</textarea>
    </div>

    <div>
        <span class="block text-sm font-medium mb-2">Permissions</span>
        <div class="space-y-4">
            @forelse ($permissions as $group => $groupPermissions)
                <div class="border rounded-md p-3" style="border-color: var(--border);">
                    <p class="text-xs font-medium uppercase tracking-wide mb-2" style="color: var(--ink-soft);">{{ $group }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($groupPermissions as $permission)
                            <label class="flex items-center gap-2 border rounded-md px-3 py-1.5 text-sm cursor-pointer" style="border-color: var(--border);">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, $checkedPermissionIds)) class="h-3.5 w-3.5 rounded">
                                {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm" style="color: var(--ink-soft);">No permissions have been created yet.</p>
            @endforelse
        </div>
    </div>
</div>