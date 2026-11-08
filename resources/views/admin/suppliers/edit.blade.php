@extends('layouts.admin')

@section('title', 'Edit Supplier — ' . $supplier->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit {{ $supplier->name }}</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.suppliers.partials.form')
    </form>
@endsection
