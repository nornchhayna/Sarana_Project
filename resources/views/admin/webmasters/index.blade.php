@extends('layouts.admin')

@section('title', 'Manage Webmasters')
@section('page-title', 'Webmaster Management')

@section('content')

{{-- STATS OVERVIEW --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Webmasters</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Accounts</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 class="text-white">{{ $stats['inactive'] }}</h3>
                <p class="text-white">Suspended Accounts</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-slash"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['recent'] }}</h3>
                <p>Added This Month</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
        </div>
    </div>
</div>

{{-- FILTERS & SEARCH --}}
<div class="card card-primary card-outline mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter Webmasters
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.webmasters.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> Add New Webmaster
            </a>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.webmasters.index') }}">
            <div class="row align-items-end">

                {{-- Search --}}
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="form-group mb-0">
                        <label for="search">Search</label>
                        <div class="input-group">
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Search by name, email, or phone..."
                            >
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-3 mb-2 mb-md-0">
                    <div class="form-group mb-0">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Suspended</option>
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="col-md-3">
                    <div class="form-group mb-0 d-flex gap-2">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('admin.webmasters.index') }}" class="btn btn-default">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- WEBMASTERS TABLE --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-shield mr-2"></i>
            Webmaster List
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $webmasters->total() }} Webmasters</span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-right" style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($webmasters as $webmaster)
                        <tr>
                            {{-- Avatar --}}
                            <td>
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center font-weight-bold"
                                     style="width: 40px; height: 40px; font-size: 14px;">
                                    {{ strtoupper(substr($webmaster->name, 0, 2)) }}
                                </div>
                            </td>

                            {{-- Name --}}
                            <td>
                                <a href="{{ route('admin.webmasters.show', $webmaster) }}" class="font-weight-bold text-dark">
                                    {{ $webmaster->name }}
                                </a>
                                @if($webmaster->id === auth()->id())
                                    <span class="badge badge-secondary ml-1">You</span>
                                @endif
                                <div>
                                    <span class="badge badge-warning text-dark">Webmaster</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td>
                                <a href="mailto:{{ $webmaster->email }}" class="text-muted">
                                    {{ $webmaster->email }}
                                </a>
                            </td>

                            {{-- Phone --}}
                            <td>{{ $webmaster->phone ?: '—' }}</td>

                            {{-- Status --}}
                            <td>
                                @if($webmaster->is_active)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-ban mr-1"></i> Suspended
                                    </span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td>
                                <small class="text-muted">
                                    {{ $webmaster->created_at ? $webmaster->created_at->format('M d, Y') : '—' }}
                                </small>
                            </td>

                            {{-- Actions --}}
                            <td class="text-right">
                                <div class="btn-group">
                                    {{-- View --}}
                                    <a href="{{ route('admin.webmasters.show', $webmaster) }}" class="btn btn-sm btn-info" title="View Dossier">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.webmasters.edit', $webmaster) }}" class="btn btn-sm btn-warning" title="Edit Webmaster">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Toggle Status --}}
                                    @if($webmaster->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.webmasters.toggleStatus', $webmaster) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $webmaster->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    title="{{ $webmaster->is_active ? 'Suspend Account' : 'Activate Account' }}"
                                                    onclick="return confirm('Are you sure you want to {{ $webmaster->is_active ? 'suspend' : 'activate' }} this webmaster?')">
                                                <i class="fas {{ $webmaster->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.webmasters.destroy', $webmaster) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Webmaster"
                                                    onclick="return confirm('Permanently delete webmaster &quot;{{ $webmaster->name }}&quot;? This cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-user-shield fa-3x mb-3 text-secondary"></i>
                                    <h5>No Webmasters Found</h5>
                                    <p class="mb-3">
                                        @if(request()->anyFilled(['search', 'status']))
                                            No webmasters matched your filter criteria.
                                        @else
                                            There are no webmaster accounts currently registered.
                                        @endif
                                    </p>
                                    <a href="{{ route('admin.webmasters.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Add New Webmaster
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($webmasters->hasPages())
        <div class="card-footer clearfix">
            {{ $webmasters->links() }}
        </div>
    @endif
</div>

@endsection
