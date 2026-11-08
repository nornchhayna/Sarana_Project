@extends('layouts.admin')

@section('title', 'Edit Purchase Order ' . $purchaseOrder->po_number)

@section('content_header')
    <h1>Edit {{ $purchaseOrder->po_number }}</h1>
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

    <form action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.purchase-orders.partials.form', ['products' => $products])
    </form>
@endsection