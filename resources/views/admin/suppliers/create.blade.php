@extends('layouts.admin')

@section('title', 'New Supplier')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">New Supplier</h1>
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

    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        @include('admin.suppliers.partials.form')
    </form>
@endsection
