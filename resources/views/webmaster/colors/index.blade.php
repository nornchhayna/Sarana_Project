@extends('layouts.webmaster')

@section('title', 'Colors')
@section('page-title', 'Colors')

@section('content')

<div class="container-fluid">

    {{-- =========================
         TOOLBAR
    ========================== --}}
    <div class="row mb-3">

        {{-- Add Color --}}
        <div class="col-md-6 mb-2 mb-md-0">

            <a
                href="{{ route('webmaster.colors.create') }}"
                class="btn btn-primary"
            >
                <i class="fas fa-plus mr-1"></i>
                Add Color
            </a>

        </div>


        {{-- Search / Filter --}}
        <div class="col-md-6">

            <form method="GET" class="form-row">

                {{-- Search --}}
                <div class="col">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search colors..."
                    >

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
         COLORS TABLE
    ========================== --}}
    <div class="card card-primary card-outline">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-palette mr-1"></i>
                Colors
            </h3>

            <div class="card-tools">

                <span class="badge badge-primary">
                    {{ $colors->total() }}
                    {{ Str::plural('Color', $colors->total()) }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>

                        <tr>

                            <th style="width: 90px;">
                                Visual
                            </th>

                            <th>
                                Color Name
                            </th>

                            <th>
                                Hex Code
                            </th>

                            <th class="text-center">
                                Assigned Variants
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

                        @forelse($colors as $color)

                            <tr>

                                {{-- Color Preview --}}
                                <td>

                                    <span
                                        class="d-inline-block rounded-circle border"
                                        style="
                                            width: 42px;
                                            height: 42px;
                                            background-color: {{ $color->hex_code ?? '#343C4A' }};
                                        "
                                        title="{{ $color->hex_code ?? 'No HEX code' }}"
                                    ></span>

                                </td>


                                {{-- Color Name --}}
                                <td>

                                    <span class="font-weight-bold">
                                        {{ $color->name }}
                                    </span>

                                </td>


                                {{-- HEX Code --}}
                                <td>

                                    @if($color->hex_code)

                                        <span class="badge badge-light border">
                                            <code>
                                                {{ $color->hex_code }}
                                            </code>
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Assigned Variants --}}
                                <td class="text-center">

                                    <span class="badge badge-light border">

                                        {{ $color->product_colors_count ?? 0 }}

                                        {{ Str::plural(
                                            'product',
                                            $color->product_colors_count ?? 0
                                        ) }}

                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($color->is_active)

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
                                            href="{{ route('webmaster.colors.edit', $color) }}"
                                            class="btn btn-sm btn-info"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('webmaster.colors.destroy', $color) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this color variant?')"
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
                                        class="fas fa-palette fa-3x mb-3 d-block"
                                    ></i>

                                    <h5>
                                        No Colors Found
                                    </h5>

                                    <p class="mb-3">
                                        No colors have been added yet.
                                    </p>

                                    <a
                                        href="{{ route('webmaster.colors.create') }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-plus mr-1"></i>
                                        Add Your First Color
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
        @if($colors->hasPages())

            <div class="card-footer clearfix">

                <div class="float-right">

                    {{ $colors->appends(request()->query())->links() }}

                </div>

            </div>

        @endif

    </div>

</div>

@endsection