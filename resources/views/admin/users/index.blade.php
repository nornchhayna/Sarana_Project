@extends('layouts.admin')
@section('title', 'User Accounts')
@section('page-title', 'User Accounts')

@section('content')

{{-- ── CUSTOM ACCOUNT MANAGEMENT OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    .user-avatar-circle { width: 36px; height: 36px; border-radius: 50%; background: var(--border, #252A3A); display: grid; place-items: center; color: var(--text-2, #8B90A7); font-weight: 700; font-size: 14px; border: 1px solid var(--border, #252A3A); }
    .role-badge { font-family: var(--mono, monospace); font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; border: 1px solid transparent; text-transform: uppercase; }
    .role-admin { background: rgba(255, 92, 122, 0.08); color: #FF5C7A; border-color: rgba(255, 92, 122, 0.15); }
    .role-webmaster { background: rgba(91, 110, 245, 0.08); color: #5B6EF5; border-color: rgba(91, 110, 245, 0.15); }
    .role-customer { background: rgba(255,255,255,0.03); color: var(--text-2); border-color: var(--border); }

    .action-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-action { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border, #252A3A); background: var(--card, #1A1E2B); color: var(--text-2, #8B90A7); display: grid; place-items: center; cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 13px; }
    .btn-action:hover { border-color: var(--accent, #5B6EF5); color: var(--text-1, #E8EAF2); background: rgba(91, 110, 245, 0.1); }
    .btn-action.toggle:hover { border-color: var(--accent-4, #F5A623); color: #F5A623; background: rgba(245, 166, 35, 0.1); }
    .btn-action.delete:hover { border-color: var(--accent-3, #FF5C7A); color: var(--accent-3); background: rgba(255, 92, 122, 0.1); }

    .table-wrap { overflow-x: auto; background: var(--card, #1A1E2B); border-radius: 12px; border: 1px solid var(--border, #252A3A); }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 14px 22px; text-align: left; border-bottom: 1px solid var(--border, #252A3A); }
    tbody tr { border-bottom: 1px solid rgba(37, 42, 58, 0.5); }
    tbody tr:hover { background: rgba(255, 255, 255, 0.02); }
    tbody td { padding: 14px 22px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; }
    
    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── FILTERS TOOLBAR ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search customer, email, phone…" style="width: 250px;">
        
        <select name="role" class="form-select" style="width: 150px;">
            <option value="">All Account Roles</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="webmaster" @selected(request('role') === 'webmaster')>Webmaster</option>
            <option value="customer" @selected(request('role') === 'customer')>Customer</option>
        </select>

        <select name="status" class="form-select" style="width: 140px;">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Suspended</option>
        </select>
        
        <button class="btn-secondary"><i class="fa-solid fa-filter"></i> Apply Filter</button>
        @if(request()->anyFilled(['search', 'role', 'status']))
            <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="border-color: transparent;">Clear</a>
        @endif
    </form>
</div>

{{-- ── USERS DATA TABLE ── --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width: 60px; padding-left: 22px;">Avatar</th>
                <th>Identity Details</th>
                <th>Email Contact</th>
                <th>Phone Number</th>
                <th>System Role</th>
                <th>Account Status</th>
                <th style="text-align: right; padding-right: 22px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="padding-left: 22px;">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                </td>
                <td>
                    <a href="{{ route('admin.users.show', $user) }}" style="color: var(--text-1, #E8EAF2); text-decoration: none; font-weight: 600;">
                        {{ $user->name }}
                    </a>
                    @if($user->id === auth()->id())
                        <span style="font-size:10px; opacity:0.5; margin-left:4px; font-family:monospace;">(You)</span>
                    @endif
                </td>
                <td style="font-family: var(--sans, sans-serif);">{{ $user->email }}</td>
                <td style="font-family: var(--mono);">{{ $user->phone ?: '—' }}</td>
                <td>
                    <span class="role-badge role-{{ $user->role }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $user->is_active ? 'status-paid' : 'status-pending' }}">
                        {{ $user->is_active ? 'Active' : 'Suspended' }}
                    </span>
                </td>
                <td style="padding-right: 22px;">
                    <div class="action-group">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-action" title="View Profile Telemetry">
                            <i class="fa-solid fa-user-gear"></i>
                        </a>
                        <!-- <a href="{{ route('admin.users.edit', $user) }}" class="btn-action" title="Modify Details">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a> -->
                        
                        @if($user->id !== auth()->id())
                            {{-- Hidden Form Elements Map for Loop Submissions --}}
        @foreach($users as $user)
             <form id="ban-form-{{ $user->id }}" action="{{ route('admin.users.ban', $user) }}" method="POST" style="display:none;">@csrf @method('PATCH')</form>
             <form id="unban-form-{{ $user->id }}" action="{{ route('admin.users.unban', $user) }}" method="POST" style="display:none;">@csrf @method('PATCH')</form>
             <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
        @endforeach
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently wipe this user profile account configuration? This action cannot be reversed.')" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button class="btn-action delete" title="Purge Profile Account">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No matching registered user credentials found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection