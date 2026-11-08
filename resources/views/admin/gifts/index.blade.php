{{-- resources/views/admin/gifts/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Gifts')
@section('page-title', 'Gifts')

@section('content')

{{-- =========================================================
     PAGE HEADER
========================================================= --}}

<div class="row mb-3">

    <div class="col-md-8">

        <h3 class="mb-1">
            Gifts
        </h3>

        <p class="text-muted mb-0">
            Manage the free items that can be attached to products.
        </p>

    </div>

    <div class="col-md-4 text-md-right mt-2 mt-md-0">

        <a
            href="{{ route('admin.gifts.create') }}"
            class="btn btn-primary">

            <i class="fas fa-plus mr-1"></i>
            Add Gift

        </a>

    </div>

</div>


{{-- =========================================================
     SUCCESS MESSAGE
========================================================= --}}

@if (session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="fas fa-check-circle mr-1"></i>

        {{ session('success') }}

        <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close">

            <span aria-hidden="true">&times;</span>

        </button>

    </div>

@endif


{{-- =========================================================
     GIFTS TABLE
========================================================= --}}

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-gift mr-2"></i>
            Gift List

        </h3>

        <div class="card-tools">

            <span class="badge badge-secondary">

                {{ $gifts->total() }}
                Gifts

            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead>

                    <tr>

                        <th style="width: 90px;">
                            Image
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th class="text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($gifts as $gift)

                        <tr>

                            {{-- Image --}}
                            <td>

                                @if ($gift->image)

                                    <img
                                        src="{{ asset('storage/' . $gift->image) }}"
                                        alt="{{ $gift->name }}"
                                        class="img-thumbnail"
                                        style="
                                            width: 55px;
                                            height: 55px;
                                            object-fit: cover;
                                        "
                                    >

                                @else

                                    <div
                                        class="bg-light d-flex align-items-center justify-content-center"
                                        style="
                                            width: 55px;
                                            height: 55px;
                                            border-radius: 4px;
                                        ">

                                        <i class="fas fa-gift text-muted"></i>

                                    </div>

                                @endif

                            </td>


                            {{-- Name --}}
                            <td>

                                <strong>
                                    {{ $gift->name }}
                                </strong>

                                @if ($gift->description)

                                    <div
                                        class="text-muted small mt-1"
                                        style="
                                            max-width: 350px;
                                            overflow: hidden;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        ">

                                        {{ $gift->description }}

                                    </div>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td>

                                @if ($gift->is_active)

                                    <span class="badge badge-success">

                                        <i class="fas fa-check mr-1"></i>
                                        Active

                                    </span>

                                @else

                                    <span class="badge badge-secondary">

                                        <i class="fas fa-minus-circle mr-1"></i>
                                        Inactive

                                    </span>

                                @endif

                            </td>


                            {{-- Created --}}
                            <td>

                                <span class="text-muted">

                                    {{ $gift->created_at->format('M d, Y') }}

                                </span>

                            </td>


                            {{-- Actions --}}
                            <td class="text-right">

                                <a
                                    href="{{ route('admin.gifts.edit', $gift) }}"
                                    class="btn btn-info btn-sm"
                                    title="Edit Gift">

                                    <i class="fas fa-edit"></i>

                                </a>


                                <form
                                    action="{{ route('admin.gifts.destroy', $gift) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this gift? This cannot be undone.');">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Delete Gift">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i class="fas fa-gift fa-3x mb-3"></i>

                                    <h5>
                                        No Gifts Yet
                                    </h5>

                                    <p class="mb-3">
                                        Create your first gift to attach
                                        it to products.
                                    </p>

                                    <a
                                        href="{{ route('admin.gifts.create') }}"
                                        class="btn btn-primary btn-sm">

                                        <i class="fas fa-plus mr-1"></i>
                                        Create Gift

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =====================================================
         PAGINATION
    ====================================================== --}}

    @if ($gifts->hasPages())

        <div class="card-footer">

            <div class="float-right">

                {{ $gifts->links() }}

            </div>

        </div>

    @endif

</div>

@endsection