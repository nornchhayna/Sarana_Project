@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Suppliers</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Supplier
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form action="{{ route('admin.suppliers.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-5 mb-2">
                    <label class="mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm" placeholder="Name, contact person, or email">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th class="text-right">Purchase Orders</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>
                                <a href="{{ route('admin.suppliers.show', $supplier) }}">{{ $supplier->name }}</a>
                            </td>
                            <td>{{ $supplier->contact_person ?? '—' }}</td>
                            <td>{{ $supplier->phone ?? '—' }}</td>
                            <td>{{ $supplier->email ?? '—' }}</td>
                            <td>{{ $supplier->country ?? '—' }}</td>
                            <td class="text-right">{{ $supplier->purchase_orders_count }}</td>
                            <td>
                                <span class="badge badge-{{ $supplier->is_active ? 'success' : 'secondary' }}">
                                    {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-xs btn-warning">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.toggleActive', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-xs btn-{{ $supplier->is_active ? 'secondary' : 'success' }}">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                @if ($supplier->purchase_orders_count === 0)
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this supplier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
