@extends('layouts.admin')
@section('title', 'Storage Options')
@section('page-title', 'Storage Options')

@section('content')

{{-- ── NATIVE DARK THEME OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }
    
    .action-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-action { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border, #252A3A); background: var(--card, #1A1E2B); color: var(--text-2, #8B90A7); display: grid; place-items: center; cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 13px; }
    .btn-action:hover { border-color: var(--accent, #5B6EF5); color: var(--text-1, #E8EAF2); background: rgba(91, 110, 245, 0.1); }
    .btn-action.delete:hover { border-color: var(--accent-3, #FF5C7A); color: var(--accent-3, #FF5C7A); background: rgba(255, 92, 122, 0.1); }
    
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
    .table-wrap { overflow-x: auto; background: var(--card, #1A1E2B); border-radius: 12px; border: 1px solid var(--border, #252A3A); }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 14px 22px; text-align: left; border-bottom: 1px solid var(--border, #252A3A); }
    tbody tr { border-bottom: 1px solid rgba(37, 42, 58, 0.5); }
    tbody tr:hover { background: rgba(255, 255, 255, 0.02); }
    tbody td { padding: 14px 22px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; }
    
    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
</style>

<div class="page-toolbar">
    <div>
       <a href="{{ route('admin.storages.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Storage Tier
        </a>
    </div>
    
    <form class="filters" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search capacities (e.g. 128GB)…">
        
        <select name="status" class="form-select" style="width: 140px;">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        
        <button class="btn-secondary">Filter</button>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="padding-left: 22px;">Capacity Size</th>
                <th style="text-align: center;">Assigned Variants</th>
                <th>Status</th>
                <th style="text-align: right; padding-right: 22px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($storages as $storage)
            <tr>
                <td style="padding-left: 22px; font-family: var(--mono, monospace); font-weight: 700; font-size: 15px; color: var(--text-1, #E8EAF2);">
                    {{ $storage->capacity }}
                </td>
                <td style="text-align: center;">
                    <span class="status-badge" style="background: var(--bg, #0C0E14); color: var(--text-2, #8B90A7); border: 1px solid var(--border, #252A3A);">
                        {{ $storage->product_storages_count ?? 0 }} products
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $storage->is_active ? 'status-paid' : 'status-pending' }}">
                        {{ $storage->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="padding-right: 22px;">
                    <div class="action-group">
                        <a href="{{ route('admin.storages.edit', ['phoneStorage' => $storage]) }}" class="btn-action" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.storages.destroy', ['phoneStorage' => $storage]) }}" onsubmit="return confirm('Remove this storage config completely?')" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button class="btn-action delete" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No storage tier configurations added yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($storages->hasPages())
    <div class="card-footer">
        {{ $storages->links() }}
    </div>
    @endif
</div>

@endsection