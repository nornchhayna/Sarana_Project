@extends('layouts.webmaster')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')

{{-- Page Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Categories</h1>
            </div>

            {{-- <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('webmaster.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </div> --}}
        </div>
    </div>
</div>


{{-- Main Content --}}
<section class="content">
    <div class="container-fluid">

        {{-- Toolbar --}}
        <div class="row mb-3">

            {{-- Add Category --}}
            <div class="col-md-6 mb-2 mb-md-0">
                <a href="{{ route('webmaster.categories.create') }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Add Category
                </a>
            </div>

            {{-- Filters --}}
            <div class="col-md-6">
                <form method="GET" class="form-inline justify-content-md-end">

                    <div class="input-group mr-2 mb-2 mb-md-0">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search categories..."
                        >

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <select
                        name="status"
                        class="form-control mr-2 mb-2 mb-md-0"
                        style="width: 140px;"
                    >
                        <option value="">All Statuses</option>
                        <option value="1" @selected(request('status') == '1')>
                            Active
                        </option>
                        <option value="0" @selected(request('status') == '0')>
                            Inactive
                        </option>
                    </select>

                    <button type="submit" class="btn btn-secondary mb-2 mb-md-0">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>

                </form>
            </div>

        </div>


        {{-- Categories Card --}}
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-layer-group mr-1"></i>
                    Category List
                </h3>

                <div class="card-tools">
                    <span class="badge badge-info">
                        {{ $categories->total() }} Categories
                    </span>
                </div>
            </div>


            {{-- Table --}}
            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead>
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Name / Slug</th>
                                <th>Parent Category</th>
                                <th class="text-center">Products</th>
                                <th>Status</th>
                                <th class="text-center">Sort Order</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($categories as $category)

                                <tr>

                                    {{-- Image --}}
                                    <td class="align-middle">

                                        @if($category->image)

                                            <img
                                                src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}"
                                                class="img-thumbnail"
                                                style="
                                                    width: 50px;
                                                    height: 50px;
                                                    object-fit: cover;
                                                "
                                            >

                                        @else

                                            <div
                                                class="bg-light d-flex align-items-center justify-content-center rounded"
                                                style="
                                                    width: 50px;
                                                    height: 50px;
                                                "
                                            >
                                                <i class="fas fa-layer-group text-muted"></i>
                                            </div>

                                        @endif

                                    </td>


                                    {{-- Name / Slug --}}
                                    <td class="align-middle">

                                        <strong>
                                            {{ $category->name }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ $category->slug }}
                                        </small>

                                    </td>


                                    {{-- Parent --}}
                                    <td class="align-middle">

                                        @if($category->parent)

                                            <span class="text-muted">
                                                <i class="fas fa-level-up-alt mr-1"></i>
                                                {{ $category->parent->name }}
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Products --}}
                                    <td class="text-center align-middle">

                                        <span class="badge badge-secondary">
                                            {{ $category->products_count ?? 0 }}
                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td class="align-middle">

                                        @if($category->is_active)

                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i>
                                                Active
                                            </span>

                                        @else

                                            <span class="badge badge-warning">
                                                <i class="fas fa-pause mr-1"></i>
                                                Inactive
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Sort Order --}}
                                    <td class="text-center align-middle">

                                        <span class="text-muted">
                                            {{ $category->sort_order ?? 0 }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-right align-middle">

                                        <div class="btn-group">

                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('webmaster.categories.edit', $category) }}"
                                                class="btn btn-sm btn-info"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                method="POST"
                                                action="{{ route('webmaster.categories.destroy', $category) }}"
                                                onsubmit="return confirm('Delete this category?')"
                                                style="display: inline;"
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
                                    <td colspan="7" class="text-center py-5">

                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>

                                        <p class="text-muted mb-0">
                                            No categories found.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Pagination --}}
            @if(method_exists($categories, 'hasPages') && $categories->hasPages())

                <div class="card-footer clearfix">

                    <div class="float-right">
                        {{ $categories->withQueryString()->links() }}
                    </div>

                </div>

            @endif

        </div>

    </div>
</section>

@endsection