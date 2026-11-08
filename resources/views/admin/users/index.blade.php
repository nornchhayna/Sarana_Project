@extends('layouts.admin')

@section('title', 'User Accounts')
@section('page-title', 'User Accounts')

@section('content')

{{-- FILTERS --}}
<div class="card card-primary card-outline mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter Users
        </h3>
    </div>

    <div class="card-body">
        <form method="GET">
            <div class="row align-items-end">

                {{-- Search --}}
                <div class="col-md-4">
                    <div class="form-group mb-md-0">
                        <label for="search">Search</label>

                        <div class="input-group">
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Customer, email, phone..."
                            >

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Role --}}
                <div class="col-md-3">
                    <div class="form-group mb-md-0">
                        <label for="role">Role</label>

                        <select
                            id="role"
                            name="role"
                            class="form-control"
                        >
                            <option value="">All Account Roles</option>

                            <option
                                value="admin"
                                @selected(request('role') === 'admin')
                            >
                                Admin
                            </option>

                            <option
                                value="webmaster"
                                @selected(request('role') === 'webmaster')
                            >
                                Webmaster
                            </option>

                            <option
                                value="customer"
                                @selected(request('role') === 'customer')
                            >
                                Customer
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-3">
                    <div class="form-group mb-md-0">
                        <label for="status">Status</label>

                        <select
                            id="status"
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
                                Suspended
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="col-md-2">
                    <div class="form-group mb-md-0">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-filter mr-1"></i>
                            Filter
                        </button>

                        @if(request()->anyFilled(['search', 'role', 'status']))

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="btn btn-default"
                            >
                                Clear
                            </a>

                        @endif

                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


{{-- USERS TABLE --}}
<div class="card card-primary card-outline">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>
            User Accounts
        </h3>

        <div class="card-tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary mr-2">
                <i class="fas fa-plus mr-1"></i> Add User
            </a>
            <span class="badge badge-primary">
                {{ $users->total() }} Users
            </span>
        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>
                    <tr>

                        <th style="width: 70px;">
                            Avatar
                        </th>

                        <th>
                            Identity
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Status
                        </th>

                        <th
                            class="text-right"
                            style="width: 130px;"
                        >
                            Actions
                        </th>

                    </tr>
                </thead>


                <tbody>

                    @forelse($users as $user)

                        <tr>

                            {{-- AVATAR --}}
                            <td>

                                <div
                                    class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="
                                        width:42px;
                                        height:42px;
                                        font-weight:600;
                                        font-size:13px;
                                    "
                                >
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>

                            </td>


                            {{-- NAME --}}
                            <td>

                                <a
                                    href="{{ route('admin.users.show', $user) }}"
                                    class="font-weight-bold text-dark"
                                >
                                    {{ $user->name }}
                                </a>

                                @if($user->id === auth()->id())

                                    <span class="badge badge-secondary ml-1">
                                        You
                                    </span>

                                @endif

                            </td>


                            {{-- EMAIL --}}
                            <td>
                                {{ $user->email }}
                            </td>


                            {{-- PHONE --}}
                            <td>
                                {{ $user->phone ?: '—' }}
                            </td>


                            {{-- ROLE --}}
                            <td>

                                @php
                                    $roleClass = match($user->role) {
                                        'admin' => 'danger',
                                        'webmaster' => 'warning',
                                        'customer' => 'primary',
                                        default => 'secondary',
                                    };
                                @endphp

                                <span class="badge badge-{{ $roleClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($user->is_active)

                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>

                                @else

                                    <span class="badge badge-danger">
                                        <i class="fas fa-ban mr-1"></i>
                                        Suspended
                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}
                            <td class="text-right">

                                <div class="btn-group">

                                    {{-- VIEW --}}
                                    <a
                                        href="{{ route('admin.users.show', $user) }}"
                                        class="btn btn-sm btn-info"
                                        title="View User"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-warning"
                                        title="Edit User"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </a>


                                    {{-- DELETE --}}
                                    @if($user->id !== auth()->id())

                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Permanently delete this user account? This action cannot be undone.');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Delete User"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="fas fa-users-slash fa-3x mb-3"
                                    ></i>

                                    <h5>
                                        No Users Found
                                    </h5>

                                    <p class="mb-0">
                                        No registered users match your filters.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- PAGINATION --}}
    @if($users->hasPages())

        <div class="card-footer clearfix">

            {{ $users->withQueryString()->links() }}

        </div>

    @endif

</div>

@endsection