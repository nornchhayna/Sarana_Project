@extends('layouts.admin')

@section('title', 'Roles')
@section('subtitle', 'Group permissions and assign them to users')

@section('content')
    <div class="flex items-center justify-end mb-5">
        <a href="{{ route('admin.roles.create') }}" class="btn-primary text-sm font-medium px-4 py-2 rounded-md">
            Add role
        </a>
    </div>

    <div class="bg-white rounded-lg border overflow-hidden" style="border-color: var(--border);">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left" style="border-color: var(--border);">
                    <th class="px-6 py-3 font-medium" style="color: var(--ink-soft);">Role</th>
                    <th class="px-4 py-3 font-medium" style="color: var(--ink-soft);">Users</th>
                    <th class="px-4 py-3 font-medium" style="color: var(--ink-soft);">Permissions</th>
                    <th class="px-4 py-3 font-medium text-right" style="color: var(--ink-soft);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr class="border-b last:border-0" style="border-color: var(--border);">
                        <td class="px-6 py-3">
                            <div class="font-medium">{{ $role->name }}</div>
                            <div class="chip-mono inline-block px-1.5 py-0.5 rounded mt-1">{{ $role->slug }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $role->users_count }}</td>
                        <td class="px-4 py-3">{{ $role->permissions_count }}</td>
                        <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-sm font-medium hover:underline">Edit</a>
                            @unless ($role->slug === 'super-admin')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete the {{ $role->name }} role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium" style="color: var(--danger);">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center" style="color: var(--ink-soft);">
                            No roles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>
@endsection