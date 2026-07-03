@extends('layouts.admin')
@section('title', 'Feedback Profile Statement')
@section('page-title', 'Review Audit')

@section('content')

{{-- ── DETAIL LAYOUT COMPONENT STYLE DESIGN ── --}}
<style>
    .review-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .admin-card-body { padding: 24px 20px; }

    .meta-display-box { padding: 16px; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); border-radius: 8px; margin-bottom: 16px; }
    .meta-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 12px; }
    .meta-row:last-child { margin-bottom: 0; border-top: 1px dashed var(--border); padding-top: 12px; margin-top: 12px; }
    .meta-label { color: var(--text-3, #555B72); font-weight: 500; }
    .meta-val { color: var(--text-1, #E8EAF2); font-weight: 600; font-family: var(--mono, monospace); }

    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; text-decoration: none; margin-bottom: 12px; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-success-outline { background: rgba(0, 212, 170, 0.04); color: var(--accent-2, #00D4AA); border: 1px solid rgba(0, 212, 170, 0.15); }
    .btn-success-outline:hover { background: var(--accent-2, #00D4AA); color: #fff; }
    .btn-danger-outline { background: rgba(255, 92, 122, 0.04); color: var(--accent-3, #FF5C7A); border: 1px solid rgba(255, 92, 122, 0.15); }
    .btn-danger-outline:hover { background: var(--accent-3, #FF5C7A); color: #fff; }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); margin-bottom: 0; }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); }

    .star-rating-display { color: var(--accent-4, #F5A623); display: flex; gap: 4px; font-size: 16px; margin-bottom: 16px; }
    
    /* Uploaded Review Image Matrix Grid style */
    .image-evidence-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px; margin-top: 20px; }
    .evidence-thumb-container { aspect-ratio: 1; border-radius: 8px; border: 1px solid var(--border, #252A3A); overflow: hidden; background: var(--bg); }
    .evidence-thumb-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s; cursor: zoom-in; }
    .evidence-thumb-container img:hover { transform: scale(1.04); }

    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
</style>

<div class="review-layout">
    
    {{-- ── LEFT PANEL: ANALYSIS MAIN BLOCK CONTENT ── --}}
    <div class="main-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <div>Feedback Review Statement</div>
                <span class="status-badge {{ $review->is_approved ? 'status-paid' : 'status-pending' }}">
                    {{ $review->is_approved ? 'Approved Viewable' : 'Pending Verification' }}
                </span>
            </div>
            <div class="admin-card-body">
                
                {{-- Score visualization node execution --}}
                <div class="star-rating-display">
                    @for($s = 1; $s <= 5; $s++)
                        <i class="{{ $s <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                    @endfor
                </div>

                <h3 style="color: var(--text-1); margin: 0 0 12px 0; font-size: 18px; font-weight: 700;">
                    {{ $review->title }}
                </h3>

                <p style="color: var(--text-2); font-size: 14px; line-height: 1.6; margin: 0;">
                    {{ $review->description ?: 'No detailed textual description provided for this rating item.' }}
                </p>

                {{-- Image attachments array parsing block execution --}}
                @if($review->images && count($review->images) > 0)
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 28px;">
                        Uploaded Attachment Metadata Evidence
                    </div>
                    <div class="image-evidence-grid">
                        @foreach($review->images as $img)
                            <div class="evidence-thumb-container">
                                <a href="{{ asset('storage/' . $img) }}" target="_blank" title="Expand Full Resolution Asset Image">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Customer Review Asset">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL: METADATA & OPERATION SWITCH ACTIONS ── --}}
    <div class="side-col">
        <div class="admin-card">
            <div class="admin-card-header">Audit Logs Ledger</div>
            <div class="admin-card-body">
                <div class="meta-display-box">
                    <div class="meta-row">
                        <span class="meta-label">Payer Customer:</span>
                        <span class="meta-val" style="font-family: var(--sans); text-transform: none;">{{ $review->user->name ?? 'Anonymous Account' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Linked Product:</span>
                        <span class="meta-val" style="font-family: var(--sans); text-transform: none; text-align: right; max-width: 160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $review->product->name ?? '—' }}
                        </span>
                    </div>
                    @if($review->order)
                        <div class="meta-row">
                            <span class="meta-label">Verified Order:</span>
                            <span class="meta-val" style="color: var(--accent);">
                                <a href="{{ route('admin.orders.show', $review->order) }}" style="color: inherit; text-decoration: none; font-weight: bold;">
                                    #{{ $review->order->order_number }}
                                </a>
                            </span>
                        </div>
                    @endif
                    <div class="meta-row">
                        <span class="meta-label font-weight-normal">Submission Time:</span>
                        <span class="meta-val">{{ $review->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

                {{-- Structural status operational controls execution --}}
                @if(!$review->is_approved)
                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success-outline">
                            <i class="fa-solid fa-square-check"></i> Authorize & Publish Review
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-danger-outline">
                            <i class="fa-solid fa-circle-minus"></i> Revoke Approval Status
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Purge this feedback record from storage completely? This cannot be reversed.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger-outline" style="background: rgba(255, 92, 122, 0.08); font-weight: 700;">
                        <i class="fa-solid fa-trash-can"></i> Delete Permanently
                    </button>
                </form>

                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-turn-down-left" style="transform: rotate(90deg);"></i> Return to Registry List
                </a>

            </div>
        </div>
    </div>

</div>

@endsection