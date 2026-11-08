@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')

{{-- Warning --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('warning') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif


{{-- ========================================================= --}}
{{-- STATUS SUMMARY --}}
{{-- ========================================================= --}}

<div class="row">

    {{-- Unread --}}
    <div class="col-lg-4 col-md-6">

        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'unread'])) }}"
           class="text-decoration-none">

            <div class="small-box bg-warning">

                <div class="inner">
                    <h3>{{ $counts['unread'] }}</h3>
                    <p>Unread Messages</p>
                </div>

                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="small-box-footer">
                    View Unread
                    <i class="fas fa-arrow-circle-right ml-1"></i>
                </div>

            </div>

        </a>

    </div>


    {{-- Read --}}
    <div class="col-lg-4 col-md-6">

        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'read'])) }}"
           class="text-decoration-none">

            <div class="small-box bg-info">

                <div class="inner">
                    <h3>{{ $counts['read'] }}</h3>
                    <p>Read Messages</p>
                </div>

                <div class="icon">
                    <i class="fas fa-envelope-open"></i>
                </div>

                <div class="small-box-footer">
                    View Read
                    <i class="fas fa-arrow-circle-right ml-1"></i>
                </div>

            </div>

        </a>

    </div>


    {{-- Replied --}}
    <div class="col-lg-4 col-md-6">

        <a href="{{ route('admin.contacts.index', array_merge(request()->except('status'), ['status' => 'replied'])) }}"
           class="text-decoration-none">

            <div class="small-box bg-success">

                <div class="inner">
                    <h3>{{ $counts['replied'] }}</h3>
                    <p>Replied Messages</p>
                </div>

                <div class="icon">
                    <i class="fas fa-reply"></i>
                </div>

                <div class="small-box-footer">
                    View Replied
                    <i class="fas fa-arrow-circle-right ml-1"></i>
                </div>

            </div>

        </a>

    </div>

</div>


{{-- ========================================================= --}}
{{-- FILTERS --}}
{{-- ========================================================= --}}

<div class="card card-primary card-outline">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter Contact Messages
        </h3>

    </div>

    <div class="card-body">

        <form method="GET"
              action="{{ route('admin.contacts.index') }}">

            <div class="row">

                {{-- Search --}}
                <div class="col-md-6">

                    <div class="form-group">

                        <label>
                            <i class="fas fa-search mr-1"></i>
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search by name, email, or subject..."
                        >

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-3">

                    <div class="form-group">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="unread"
                                {{ request('status') === 'unread' ? 'selected' : '' }}>
                                Unread
                            </option>

                            <option value="read"
                                {{ request('status') === 'read' ? 'selected' : '' }}>
                                Read
                            </option>

                            <option value="replied"
                                {{ request('status') === 'replied' ? 'selected' : '' }}>
                                Replied
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="col-md-3">

                    <label>&nbsp;</label>

                    <div class="d-flex">

                        <button
                            type="submit"
                            class="btn btn-primary mr-2">

                            <i class="fas fa-filter mr-1"></i>
                            Filter

                        </button>

                        @if(request()->hasAny(['search', 'status']))

                            <a href="{{ route('admin.contacts.index') }}"
                               class="btn btn-secondary">

                                <i class="fas fa-times mr-1"></i>
                                Clear

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- ========================================================= --}}
{{-- CONTACT TABLE --}}
{{-- ========================================================= --}}

<form id="bulk-form"
      method="POST"
      action="{{ route('admin.contacts.bulk-mark-read') }}">

    @csrf

    <div class="card card-primary card-outline">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-envelope mr-2"></i>
                Contact Messages
            </h3>


            {{-- Bulk Actions --}}
            <div class="card-tools"
                 id="bulk-action-bar"
                 style="display:none;">

                <span class="badge badge-primary mr-2">
                    <span id="selected-count">0</span>
                    selected
                </span>

                <button
                    type="button"
                    onclick="submitBulk('{{ route('admin.contacts.bulk-mark-read') }}')"
                    class="btn btn-sm btn-info">

                    <i class="fas fa-envelope-open mr-1"></i>
                    Mark Read

                </button>

                <button
                    type="button"
                    onclick="deleteSelected()"
                    class="btn btn-sm btn-danger">

                    <i class="fas fa-trash mr-1"></i>
                    Delete

                </button>

            </div>

        </div>


        @if($contacts->count() > 0)

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>

                            <tr>

                                <th style="width:40px;">

                                    <input
                                        type="checkbox"
                                        id="select-all">

                                </th>

                                <th>Name</th>

                                <th>Subject</th>

                                <th>Status</th>

                                <th>Received</th>

                                <th class="text-right">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($contacts as $contact)

                                <tr>

                                    {{-- Checkbox --}}
                                    <td>

                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $contact->id }}"
                                            class="row-checkbox">

                                    </td>


                                    {{-- Name --}}
                                    <td>

                                        <a
                                            href="{{ route('admin.contacts.show', $contact) }}"
                                            class="text-decoration-none">

                                            <div class="d-flex align-items-center">

                                                {{-- Avatar --}}
                                                <div
                                                    class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2"
                                                    style="width:38px;height:38px;font-weight:600;">

                                                    {{ strtoupper(substr($contact->name, 0, 1)) }}

                                                </div>


                                                <div>

                                                    <div
                                                        class="{{ $contact->status === 'unread' ? 'font-weight-bold' : '' }} text-dark">

                                                        {{ $contact->name }}

                                                    </div>

                                                    <small class="text-muted">

                                                        {{ $contact->email }}

                                                    </small>

                                                </div>

                                            </div>

                                        </a>

                                    </td>


                                    {{-- Subject --}}
                                    <td>

                                        <a
                                            href="{{ route('admin.contacts.show', $contact) }}"
                                            class="text-dark">

                                            {{ $contact->subject ?? 'No subject' }}

                                        </a>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($contact->status === 'unread')

                                            <span class="badge badge-warning">
                                                <i class="fas fa-envelope mr-1"></i>
                                                Unread
                                            </span>

                                        @elseif($contact->status === 'read')

                                            <span class="badge badge-secondary">
                                                <i class="fas fa-envelope-open mr-1"></i>
                                                Read
                                            </span>

                                        @else

                                            <span class="badge badge-success">
                                                <i class="fas fa-reply mr-1"></i>
                                                Replied
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Received --}}
                                    <td>

                                        <span
                                            title="{{ $contact->created_at->format('Y-m-d H:i:s') }}">

                                            {{ $contact->created_at->diffForHumans() }}

                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-right">

                                        <div class="btn-group">

                                            <a
                                                href="{{ route('admin.contacts.show', $contact) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="View">

                                                <i class="fas fa-eye"></i>

                                            </a>


                                            <form
                                                action="{{ route('admin.contacts.destroy', $contact) }}"
                                                method="POST"
                                                onsubmit="return confirm('Delete this contact message?')">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Pagination --}}
            @if($contacts->hasPages())

                <div class="card-footer clearfix">

                    {{ $contacts->links() }}

                </div>

            @endif


        @else

            {{-- Empty --}}
            <div class="card-body">

                <div class="text-center py-5">

                    <div class="mb-3">

                        <i
                            class="fas fa-envelope-open-text fa-3x text-muted">
                        </i>

                    </div>

                    <h5>
                        No contact messages found
                    </h5>

                    <p class="text-muted mb-0">

                        @if(request()->hasAny(['search','status']))

                            Try adjusting your search or filter.

                        @else

                            New messages will appear here once submitted.

                        @endif

                    </p>

                </div>

            </div>

        @endif

    </div>

</form>

@endsection


@push('scripts')

<script>

    const selectAll = document.getElementById('select-all');

    const rowCheckboxes =
        document.querySelectorAll('.row-checkbox');

    const bulkBar =
        document.getElementById('bulk-action-bar');

    const selectedCount =
        document.getElementById('selected-count');

    const bulkForm =
        document.getElementById('bulk-form');


    function updateBulkBar() {

        const checked =
            document.querySelectorAll('.row-checkbox:checked');

        selectedCount.textContent =
            checked.length;

        if (checked.length > 0) {

            bulkBar.style.display = 'block';

        } else {

            bulkBar.style.display = 'none';

        }

    }


    selectAll?.addEventListener('change', function () {

        rowCheckboxes.forEach(function (checkbox) {

            checkbox.checked =
                selectAll.checked;

        });

        updateBulkBar();

    });


    rowCheckboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            updateBulkBar
        );

    });


    function submitBulk(actionUrl) {

        const checked =
            document.querySelectorAll('.row-checkbox:checked');

        if (checked.length === 0) {

            alert('Please select at least one message.');

            return;

        }

        bulkForm.action = actionUrl;

        bulkForm.submit();

    }


    function deleteSelected() {

        const checked =
            document.querySelectorAll('.row-checkbox:checked');

        if (checked.length === 0) {

            alert('Please select at least one message.');

            return;

        }

        if (confirm('Delete selected contact messages?')) {

            submitBulk(
                '{{ route('admin.contacts.bulk-destroy') }}'
            );

        }

    }

</script>

@endpush