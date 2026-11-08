@extends('layouts.admin')

@section('title', 'Storage Options')
@section('page-title', 'Storage Options')

@section('content')

<div class="row">
    <div class="col-12">

        {{-- Header / Toolbar --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hdd mr-2"></i>
                    Storage Options
                </h3>

                <div class="card-tools">
                    <a href="{{ route('admin.storages.create') }}"
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i>
                        Add Storage Tier
                    </a>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row">

                        {{-- Search --}}
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        value="{{ request('search') }}"
                                        class="form-control"
                                        placeholder="Search capacities (e.g. 128GB)..."
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status"
                                        id="status"
                                        class="form-control">
                                    <option value="">All Statuses</option>

                                    <option value="active"
                                        @selected(request('status') === 'active')}>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(request('status') === 'inactive')}>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>

                                <div>
                                    <button type="submit"
                                            class="btn btn-primary">
                                        <i class="fas fa-filter"></i>
                                        Filter
                                    </button>

                                    <a href="{{ url()->current() }}"
                                       class="btn btn-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead>
                            <tr>
                                <th style="width: 30%;">
                                    Capacity Size
                                </th>

                                <th class="text-center"
                                    style="width: 25%;">
                                    Assigned Variants
                                </th>

                                <th style="width: 20%;">
                                    Status
                                </th>

                                <th class="text-right"
                                    style="width: 25%;">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($storages as $storage)

                                <tr>

                                    {{-- Capacity --}}
                                    <td>
                                        <strong>
                                            <code>
                                                {{ $storage->capacity }}
                                            </code>
                                        </strong>
                                    </td>

                                    {{-- Products --}}
                                    <td class="text-center">
                                        <span class="badge badge-secondary">
                                            {{ $storage->product_storages_count ?? 0 }}
                                            products
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if($storage->is_active)

                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i>
                                                Active
                                            </span>

                                        @else

                                            <span class="badge badge-warning">
                                                <i class="fas fa-times-circle"></i>
                                                Inactive
                                            </span>

                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-right">

                                        <a href="{{ route('admin.storages.edit', ['phoneStorage' => $storage]) }}"
                                           class="btn btn-info btn-sm"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.storages.destroy', ['phoneStorage' => $storage]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Remove this storage config completely?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center py-5">

                                        <div class="text-muted">
                                            <i class="fas fa-hdd fa-3x mb-3"></i>

                                            <h5>
                                                No Storage Tiers Found
                                            </h5>

                                            <p class="mb-0">
                                                No storage tier configurations
                                                have been added yet.
                                            </p>
                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

            {{-- Pagination --}}
            @if($storages->hasPages())

                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $storages->links() }}
                    </div>
                </div>

            @endif

        </div>

    </div>
</div>

@endsection