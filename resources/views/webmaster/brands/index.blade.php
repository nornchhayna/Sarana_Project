@extends('layouts.webmaster')

@section('title', 'Brands')
@section('page-title', 'Brands')

@section('content')

<div class="container-fluid">

    {{-- =========================
         TOOLBAR
    ========================== --}}
    <div class="row mb-3">

        {{-- Add Brand --}}
        <div class="col-md-6 mb-2 mb-md-0">
            <a href="{{ route('webmaster.brands.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i>
                Add Brand
            </a>
        </div>

        {{-- Filters --}}
        <div class="col-md-6">

            <form method="GET" class="form-row">

                {{-- Search --}}
                <div class="col">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search brands..."
                    >
                </div>

                {{-- Status --}}
                <div class="col-auto">

                    <select
                        name="status"
                        class="form-control"
                    >
                        <option value="">All Statuses</option>

                        <option
                            value="active"
                            @selected(request('status') === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected(request('status') === 'inactive')
                        >
                            Inactive
                        </option>
                    </select>

                </div>

                {{-- Filter Button --}}
                <div class="col-auto">

                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================
         BRANDS TABLE
    ========================== --}}
    <div class="card card-primary card-outline">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-tags mr-1"></i>
                Brands
            </h3>

            <div class="card-tools">

                <span class="badge badge-primary">
                    {{ $brands->total() }}
                    {{ Str::plural('Brand', $brands->total()) }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>

                        <tr>

                            <th style="width: 80px;">
                                Logo
                            </th>

                            <th>
                                Name / Slug
                            </th>

                            <th class="text-center">
                                Phone Model Code
                            </th>

                            <th class="text-center">
                                Products Count
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-right" style="width: 120px;">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($brands as $brand)

                            <tr>

                                {{-- Logo --}}
                                <td>

                                    @if($brand->logo)

                                        <img
                                            src="{{ asset('storage/' . $brand->logo) }}"
                                            alt="{{ $brand->name }}"
                                            class="img-circle"
                                            style="
                                                width:45px;
                                                height:45px;
                                                object-fit:contain;
                                                background:#f8f9fa;
                                                padding:5px;
                                            "
                                        >

                                    @else

                                        <div
                                            class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="
                                                width:45px;
                                                height:45px;
                                            "
                                        >
                                            <i class="fas fa-bookmark text-muted"></i>
                                        </div>

                                    @endif

                                </td>


                                {{-- Name / Slug --}}
                                <td>

                                    <div class="font-weight-bold">
                                        {{ $brand->name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $brand->slug }}
                                    </small>

                                </td>


                                {{-- Phone Model Code --}}
                                <td class="text-center">

                                    <span class="badge badge-light border">
                                        {{ $brand->phone_models_count ?? 0 }}
                                    </span>

                                </td>


                                {{-- Products Count --}}
                                <td class="text-center">

                                    <span class="badge badge-light border">
                                        {{ $brand->products_count ?? 0 }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($brand->is_active)

                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>

                                    @else

                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="text-right">

                                    <div class="btn-group">

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('webmaster.brands.edit', $brand) }}"
                                            class="btn btn-sm btn-info"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('webmaster.brands.destroy', $brand) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this brand? This cannot be undone.')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Delete"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="fas fa-tags fa-3x mb-3 d-block"
                                    ></i>

                                    <h5>No Brands Found</h5>

                                    <p class="mb-3">
                                        There are no brands matching your search.
                                    </p>

                                    <a
                                        href="{{ route('webmaster.brands.create') }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-plus mr-1"></i>
                                        Add Your First Brand
                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================
             PAGINATION
        ========================== --}}
        @if($brands->hasPages())

            <div class="card-footer clearfix">

                <div class="float-right">
                    {{ $brands->appends(request()->query())->links() }}
                </div>

            </div>

        @endif

    </div>

</div>

@endsection