@extends('layouts.admin')

@section('title', 'RAM Options')
@section('page-title', 'RAM Options')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            {{-- Card Header --}}
            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-memory mr-2"></i>
                    RAM Options
                </h3>

                <div class="card-tools">

                    <a href="{{ route('admin.rams.create') }}"
                       class="btn btn-primary btn-sm">

                        <i class="fas fa-plus mr-1"></i>
                        Add RAM Tier

                    </a>

                </div>

            </div>


            {{-- Filters --}}
            <div class="card-body">

                <form method="GET"
                      action="{{ url()->current() }}">

                    <div class="row">

                        {{-- Search --}}
                        <div class="col-md-5">

                            <div class="form-group">

                                <label for="search">
                                    Search
                                </label>

                                <div class="input-group">

                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        value="{{ request('search') }}"
                                        class="form-control"
                                        placeholder="Search capacities (e.g. 8GB)..."
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

                                <label for="status">
                                    Status
                                </label>

                                <select
                                    name="status"
                                    id="status"
                                    class="form-control">

                                    <option value="">
                                        All Statuses
                                    </option>

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


                        {{-- Filter Buttons --}}
                        <div class="col-md-4">

                            <div class="form-group">

                                <label>&nbsp;</label>

                                <div>

                                    <button type="submit"
                                            class="btn btn-primary">

                                        <i class="fas fa-filter mr-1"></i>
                                        Filter

                                    </button>

                                    <a href="{{ url()->current() }}"
                                       class="btn btn-secondary">

                                        <i class="fas fa-sync-alt mr-1"></i>
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

                                <th>
                                    Capacity Size
                                </th>

                                <th class="text-center">
                                    Assigned Products
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-right">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($rams as $ram)

                                <tr>

                                    {{-- Capacity --}}
                                    <td>

                                        <strong>
                                            <code>
                                                {{ $ram->capacity }}
                                            </code>
                                        </strong>

                                        @if($ram->label)

                                            <small class="text-muted ml-2">
                                                ({{ $ram->label }})
                                            </small>

                                        @endif

                                    </td>


                                    {{-- Products --}}
                                    <td class="text-center">

                                        <span class="badge badge-secondary">

                                            {{ $ram->product_rams_count ?? 0 }}

                                            products

                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($ram->is_active)

                                            <span class="badge badge-success">

                                                <i class="fas fa-check-circle mr-1"></i>
                                                Active

                                            </span>

                                        @else

                                            <span class="badge badge-warning">

                                                <i class="fas fa-times-circle mr-1"></i>
                                                Inactive

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-right">

                                        <a href="{{ route('admin.rams.edit', $ram) }}"
                                           class="btn btn-info btn-sm"
                                           title="Edit">

                                            <i class="fas fa-edit"></i>

                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route('admin.rams.destroy', $ram) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Remove this RAM config completely?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
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

                                            <i class="fas fa-memory fa-3x mb-3"></i>

                                            <h5>
                                                No RAM Tiers Found
                                            </h5>

                                            <p class="mb-0">
                                                No RAM tier configurations
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
            @if($rams->hasPages())

                <div class="card-footer clearfix">

                    <div class="float-right">

                        {{ $rams->links() }}

                    </div>

                </div>

            @endif

        </div>

    </div>
</div>

@endsection