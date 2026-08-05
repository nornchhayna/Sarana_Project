@extends('layouts.admin')

@section('title', 'Contact Messages')

@push('styles')
<style>
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .summary-card {
        position: relative; display: block; background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 20px; transition: box-shadow 0.15s, border-color 0.15s;
    }
    .summary-card:hover { box-shadow: 0 4px 14px rgba(37,61,78,0.08); }
    .summary-card.active { border-color: var(--orange); box-shadow: 0 0 0 2px rgba(240,96,63,0.12); }
    .summary-top { display: flex; align-items: flex-start; justify-content: space-between; }
    .summary-label { font-size: 13px; font-weight: 600; color: var(--text-2); margin-bottom: 4px; }
    .summary-value { font-size: 28px; font-weight: 700; font-family: var(--font-mono); color: var(--text-1); }
    .summary-icon { width: 44px; height: 44px; border-radius: 10px; display: grid; place-items: center; font-size: 17px; }
    .summary-icon.unread  { background: rgba(63,111,238,0.10); color: var(--blue); }
    .summary-icon.read    { background: var(--input-bg); color: var(--text-2); }
    .summary-icon.replied { background: rgba(31,157,99,0.10); color: var(--success); }
    .pulse-dot { position: absolute; top: 12px; right: 12px; width: 8px; height: 8px; border-radius: 50%; background: var(--blue); animation: pulse 1.6s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.35; } }

    .status-neutral { background: var(--input-bg); color: var(--text-2); }

    .contact-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--navy), var(--blue));
        color: #fff; font-size: 12px; font-weight: 700;
        display: grid; place-items: center;
    }

    .bulk-bar {
        display: none; align-items: center; justify-content: space-between;
        padding: 12px 22px; background: var(--input-bg); border-bottom: 1px solid var(--border);
    }
    .bulk-bar.visible { display: flex; }
    .bulk-bar .count { font-size: 13px; font-weight: 600; color: var(--blue); }

    .empty-state { text-align: center; padding: 64px 16px; }
    .empty-icon { width: 60px; height: 60px; margin: 0 auto 14px; border-radius: 14px; background: var(--input-bg); display: grid; place-items: center; color: var(--text-3); font-size: 22px; }

    .alert-warning { background: #FEF6E7; border: 1px solid #F5D999; color: #9A6B00; }
</style>
@endpush

@section('content')

    <div class="page-toolbar">
        <div>
            <div class="page-title" style="margin-bottom:2px;">Contact Messages</div>
            <div style="font-size:12px; color:var(--text-3);">Manage inquiries submitted through the contact form</div>
        </div>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('warning') }}
        </div>
    @endif

    {{-- Status summary --}}
    <div class="summary-grid">
        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'unread'])) }}"
           class="summary-card {{ request('status') === 'unread' ? 'active' : '' }}">
            @if($counts['unread'] > 0)<span class="pulse-dot"></span>@endif
            <div class="summary-top">
                <div>
                    <div class="summary-label">Unread</div>
                    <div class="summary-value">{{ $counts['unread'] }}</div>
                </div>
                <div class="summary-icon unread"><i class="fa-solid fa-envelope"></i></div>
            </div>
        </a>

        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'read'])) }}"
           class="summary-card {{ request('status') === 'read' ? 'active' : '' }}">
            <div class="summary-top">
                <div>
                    <div class="summary-label">Read</div>
                    <div class="summary-value">{{ $counts['read'] }}</div>
                </div>
                <div class="summary-icon read"><i class="fa-solid fa-envelope-open"></i></div>
            </div>
        </a>

        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'replied'])) }}"
           class="summary-card {{ request('status') === 'replied' ? 'active' : '' }}">
            <div class="summary-top">
                <div>
                    <div class="summary-label">Replied</div>
                    <div class="summary-value">{{ $counts['replied'] }}</div>
                </div>
                <div class="summary-icon replied"><i class="fa-solid fa-reply"></i></div>
            </div>
        </a>
    </div>

    {{-- Filters --}}
    <div class="admin-card">
        <div class="admin-card-body" style="padding: 16px 20px;">
            <form method="GET" action="{{ route('admin.contacts.index') }}" style="display:flex; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:220px; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, email, or subject…"
                           class="form-input" style="width:100%; padding-left:34px;">
                </div>

                <select name="status" onchange="this.form.submit()" class="form-select">
                    <option value="">All statuses</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                </select>

                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.contacts.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <form id="bulk-form" method="POST" action="{{ route('admin.contacts.bulk-mark-read') }}">
        @csrf
        <div class="admin-card" style="margin-top:16px;">

            <div id="bulk-action-bar" class="bulk-bar">
                <span class="count"><span id="selected-count">0</span> selected</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" onclick="submitBulk('{{ route('admin.contacts.bulk-mark-read') }}')" class="btn-secondary" style="padding:7px 14px;">
                        Mark read
                    </button>
                    <button type="button" onclick="if(confirm('Delete selected contacts?')) submitBulk('{{ route('admin.contacts.bulk-destroy') }}')"
                            class="btn-secondary" style="padding:7px 14px; color:var(--danger); border-color:var(--danger);">
                        Delete
                    </button>
                </div>
            </div>

            @if($contacts->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:36px;"><input type="checkbox" id="select-all"></th>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $contact->id }}" class="row-checkbox"></td>
                                    <td>
                                        <a href="{{ route('admin.contacts.show', $contact) }}" style="display:flex; align-items:center; gap:10px;">
                                            <div class="contact-avatar">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
                                            <div>
                                                <div style="font-size:13px; font-weight:{{ $contact->status === 'unread' ? '700' : '600' }}; color:var(--text-1);">
                                                    {{ $contact->name }}
                                                </div>
                                                <div style="font-size:11.5px; color:var(--text-3);">{{ $contact->email }}</div>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.contacts.show', $contact) }}" style="color:var(--text-2);">
                                            {{ $contact->subject ?? 'No subject' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($contact->status === 'unread')
                                            <span class="status-badge status-shipped">Unread</span>
                                        @elseif($contact->status === 'read')
                                            <span class="status-badge status-neutral">Read</span>
                                        @else
                                            <span class="status-badge status-paid">Replied</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('admin.contacts.show', $contact) }}" title="View" class="btn-action">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                                                  onsubmit="return confirm('Delete this contact message?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete" class="btn-action delete">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $contacts->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div style="font-weight:600; color:var(--text-1);">No contact messages found</div>
                    <div style="font-size:13px; color:var(--text-3); margin-top:4px;">
                        @if(request()->hasAny(['search','status']))
                            Try adjusting your search or filter.
                        @else
                            New messages will appear here once submitted.
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </form>

@endsection

@push('scripts')
<script>
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCount = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-form');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        selectedCount.textContent = checked.length;
        bulkBar.classList.toggle('visible', checked.length > 0);
    }

    selectAll?.addEventListener('change', () => {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkBar();
    });

    rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));

    function submitBulk(actionUrl) {
        bulkForm.action = actionUrl;
        bulkForm.submit();
    }
</script>
@endpush