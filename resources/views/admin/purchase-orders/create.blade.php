@extends('layouts.admin')

@section('title', 'New Purchase Order')

@section('content_header')
    <h1>New Purchase Order</h1>
@endsection

@section('content')
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

    <form action="{{ route('admin.purchase-orders.store') }}" method="POST">
        @csrf
        @include('admin.purchase-orders.partials.form', ['products' => $products])
    </form>
@endsection