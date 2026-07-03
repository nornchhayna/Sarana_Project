@extends('layouts.admin')
@section('title', 'Product Reviews')
@section('page-title', 'Customer Feedback')

@section('content')

{{-- ── REVIEWS DASHBOARD STYLES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    /* Overview Metadata Grid Layout */
    .review-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-metric-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; }
    .metric-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; color: var(--text-3, #555B72); margin-bottom: 4px; }
    .metric-value { font-size: 24px; font-family: var(--mono, monospace); font-weight: 700; color: var(--text-1, #E8EAF2); }
    .metric-icon-box { width: 44px; height: 44px; border-radius: 8px; display: grid; place-items: center; font-size: 16px; }

    /* Star Config */
    .star-rating-display { color: var(--accent-4, #F5A623); display: flex; gap: 2px; font-size: 13px; }
    
    .text-truncated { max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-2, #8B90A7); font-size: 13px; }

    .action-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-action { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border, #252A3A); background: var(--card, #1A1E2B); color: var(--text-2, #8B90A7); display: grid; place-items: center; cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 13px; }
    .btn-action:hover { border-color: var(--accent, #5B6EF5); color: var(--text-1, #E8EAF2); background: rgba(91, 110, 245, 0.1); }
    .btn-action.approve:hover { border-color: var(--accent-2, #00D4AA); color: var(--accent-2); background: rgba(0, 212, 170, 0.1); }
    .btn-action.delete:hover { border-color: var(--accent-3, #FF5C7A); color: var(--accent-3); background: rgba(255, 92, 122, 0.1); }

    .table-wrap { overflow-x: auto; background: var(--card, #1A1E2B); border-radius: 12px; border: 1px solid var(--border, #252A3A); }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 14px 22px; text-align: left; border-bottom: 1px solid var(--border, #252A3A); }
    tbody tr { border-bottom: 1px solid rgba(37, 42, 58, 0.5); }
    tbody tr:hover { background: rgba(255, 255, 255, 0.02); }
    tbody td { padding: 14px 22px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; }
    
    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── REVIEWS METRIC INSIGHT CARDS ── --}}
<div class="review-stats-grid">
    <div class="stat-metric-card">
        <div>
            <div class="metric-title">Awaiting Approval</div>
            <div class="metric-value" style="color: var(--accent-4, #F5A623);">{{ $stats['pending'] }}</div>
        </div>
        <div class="metric-icon-box" style="background: rgba(245, 166, 35, 0.1); color: #F5A623;">
            <i class="fa-solid fa-comment-slash"></i>
        </div>
    </div>
    
    <div class="stat-metric-card">
        <div>
            <div class="metric-title">Approved Reviews</div>
            <div class="metric-value" style="color: var(--accent-2, #00D4AA);">{{ $stats['approved'] }}</div>
        </div>
        <div class="metric-icon-box" style="background: rgba(0, 212, 170, 0.1); color: #00D4AA;">
            <i class="fa-solid fa-comment-check"></i>
        </div>
    </div>

    <div class="stat-metric-card">
        <div>
            <div class="metric-title">Average Store Rating</div>
            <div class="metric-value" style="display: flex; align-items: center; gap: 8px;">
                <span>{{ $stats['avg'] }}</span>
                <span style="font-size:14px; color: var(--accent-4);"><i class="fa-solid fa-star"></i></span>
            </div>
        </div>
        <div class="metric-icon-box" style="background: rgba(91, 110, 245, 0.1); color: var(--accent);">
            <i class="fa-solid fa-star-half-stroke"></i>
        </div>
    </div>
</div>

{{-- ── PARAMETER FILTERS ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%; justify-content: space-between;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search title, user, item…" style="width: 240px;">
            
            <select name="rating" class="form-select" style="width: 130px;">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} Stars</option>
                @endfor
            </select>

            <select name="status" class="form-select" style="width: 150px;">
                <option value="">All Statuses</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            </select>
            
            <button class="btn-secondary"><i class="fa-solid fa-filter"></i> Filter</button>
            @if(request()->anyFilled(['search', 'rating', 'status']))
                <a href="{{ route('admin.reviews.index') }}" class="btn-secondary" style="border-color: transparent;">Clear</a>
            @endif
        </div>

        @if($reviews->count() > 0)
            <button type="button" onclick="document.getElementById('bulk-approve-form').submit();" class="btn-primary">
                <i class="fa-solid fa-check-double"></i> Approve Selected
            </button>
        @endif
    </form>
</div>

{{-- ── DATATABLE INTERACTIVE WRAPPER ── --}}
<form id="bulk-approve-form" method="POST" action="{{ route('admin.reviews.bulkApprove') }}">
    @csrf
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; padding-left: 22px;">
                        <input type="checkbox" onclick="let checks = document.querySelectorAll('.bulk-select'); checks.forEach(c => c.checked = this.checked);" style="accent-color: var(--accent); cursor: pointer;">
                    </th>
                    <th>Product Item</th>
                    <th>Customer Payer</th>
                    <th>Score Rating</th>
                    <th>Summary Details Snippet</th>
                    <th>Status</th>
                    <th style="text-align: right; padding-right: 22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td style="padding-left: 22px;">
                        <input type="checkbox" name="ids[]" value="{{ $review->id }}" class="bulk-select" style="accent-color: var(--accent); cursor: pointer;">
                    </td>
                    <td>
                        <span style="color: var(--text-1, #E8EAF2); font-weight: 600;">
                            {{ $review->product->name ?? 'Deleted Item Reference' }}
                        </span>
                    </td>
                    <td style="font-weight: 500;">
                        {{ $review->user->name ?? 'Anonymous' }}
                    </td>
                    <td>
                        <div class="star-rating-display">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="{{ $s <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </div>
                    </td>
                    <td>
                        <div class="text-truncated" title="{{ $review->description }}">
                            <strong style="color: var(--text-1);">{{ $review->title }}</strong> — {{ $review->description }}
                        </div>
                    </td>
                    <td>
                        <span class="status-badge {{ $review->is_approved ? 'status-paid' : 'status-pending' }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td style="padding-right: 22px;">
                        <div class="action-group">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn-action" title="View Review Detail Card">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            
                            @if(!$review->is_approved)
                                <a href="{{ route('admin.reviews.approve', $review) }}" class="btn-action approve" title="Verify Approve" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $review->id }}').submit();">
                                    <i class="fa-solid fa-circle-check"></i>
                                </a>
                            @endif

                            <a href="{{ route('admin.reviews.destroy', $review) }}" class="btn-action delete" title="Remove Record" onclick="event.preventDefault(); if(confirm('Permanently purge this item review configuration data?')) { document.getElementById('delete-form-{{ $review->id }}').submit(); }">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No feedback review items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($reviews->hasPages())
        <div class="card-footer">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</form>

{{-- Hidden Form Elements Map for Individual Action Submissions inside Loops --}}
@foreach($reviews as $review)
    @if(!$review->is_approved)
        <form id="approve-form-{{ $review->id }}" action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display:none;">@csrf @method('PUT')</form>
    @endif
    <form id="delete-form-{{ $review->id }}" action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
@endforeach

@endsection