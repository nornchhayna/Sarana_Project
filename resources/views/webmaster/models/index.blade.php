@extends('layouts.webmaster')

@section('title', 'Phone Models')
@section('page-title', 'Phone Models')

@section('content')

<div class="container-fluid">

    {{-- =========================
         TOOLBAR
    ========================== --}}
    <div class="row mb-3">

        {{-- Add Phone Model --}}
        <div class="col-md-5 mb-2 mb-md-0">

            <a
                href="{{ route('webmaster.models.create') }}"
                class="btn btn-primary"
            >
                <i class="fas fa-plus mr-1"></i>
                Add Phone Model
            </a>

        </div>


        {{-- Filters --}}
        <div class="col-md-7">

            <form method="GET" class="form-row">

                {{-- Search --}}
                <div class="col">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search models..."
                    >

                </div>


                {{-- Brand --}}
                <div class="col-auto">

                    <select
                        name="brand"
                        class="form-control"
                    >

                        <option value="">
                            All Brands
                        </option>

                        @foreach($brands as $brand)

                            <option
                                value="{{ $brand->id }}"
                                @selected(request('brand') == $brand->id)
                            >
                                {{ $brand->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="col-auto">

                    <select
                        name="status"
                        class="form-control"
                    >

                        <option value="">
                            All Statuses
                        </option>

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


                {{-- Filter --}}
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
         PHONE MODELS TABLE
    ========================== --}}
    <div class="card card-primary card-outline">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-mobile-alt mr-1"></i>
                Phone Models
            </h3>

            <div class="card-tools">

                <span class="badge badge-primary">
                    {{ $models->total() }}
                    {{ Str::plural('Model', $models->total()) }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>

                        <tr>

                            <th>
                                Model Name / Slug
                            </th>

                            <th>
                                Brand Parent
                            </th>

                            <th class="text-center">
                                Assigned Products
                            </th>

                            <th>
                                Status
                            </th>

                            <th
                                class="text-right"
                                style="width: 120px;"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($models as $model)

                            <tr>

                                {{-- Model --}}
                                <td>

                                    <div class="font-weight-bold">
                                        {{ $model->name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $model->slug }}
                                    </small>

                                </td>


                                {{-- Brand --}}
                                <td>

                                    @if($model->brand)

                                        <span class="font-weight-bold">
                                            {{ $model->brand->name }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Products --}}
                                <td class="text-center">

                                    <span class="badge badge-light border">

                                        {{ $model->products_count ?? 0 }}

                                        {{ Str::plural(
                                            'entry',
                                            $model->products_count ?? 0
                                        ) }}

                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($model->is_active)

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
                                            href="{{ route('webmaster.models.edit', $model) }}"
                                            class="btn btn-sm btn-info"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('webmaster.models.destroy', $model) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this phone model configuration?')"
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
                                    colspan="5"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="fas fa-mobile-alt fa-3x mb-3 d-block"
                                    ></i>

                                    <h5>
                                        No Phone Models Found
                                    </h5>

                                    <p class="mb-3">
                                        No model records have been registered yet.
                                    </p>

                                    <a
                                        href="{{ route('webmaster.models.create') }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-plus mr-1"></i>
                                        Add Your First Phone Model
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
        @if($models->hasPages())

            <div class="card-footer clearfix">

                <div class="float-right">

                    {{ $models->appends(request()->query())->links() }}

                </div>

            </div>

        @endif

    </div>

</div>

@endsection