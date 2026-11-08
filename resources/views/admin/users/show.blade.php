@extends('layouts.admin')
@section('title', 'User Profile File')
@section('page-title', 'User Profile Dossier')

@section('content')



<div class="container-fluid">

    <div class="row">

        {{-- PROFILE --}}
        <div class="col-lg-4">

            <div class="card card-primary card-outline">

                <div class="card-body box-profile">

                    <div class="text-center">

                        <div class="bg-primary rounded-circle mx-auto
                                    d-flex align-items-center justify-content-center
                                    text-white font-weight-bold"
                             style="width:90px;height:90px;font-size:28px;">

                            {{ strtoupper(substr($user->name, 0, 2)) }}

                        </div>

                    </div>

                    <h3 class="profile-username text-center mt-3">
                        {{ $user->name }}
                    </h3>

                    <p class="text-muted text-center">
                        {{ ucfirst($user->role) }}
                    </p>


                    <ul class="list-group list-group-unbordered mb-3">

                        <li class="list-group-item">
                            <b>Email</b>
                            <span class="float-right">
                                {{ $user->email }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Phone</b>
                            <span class="float-right">
                                {{ $user->phone ?: '—' }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Gender</b>
                            <span class="float-right">
                                {{ $user->gender ?: '—' }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>City</b>
                            <span class="float-right">
                                {{ $user->city ?: '—' }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Province</b>
                            <span class="float-right">
                                {{ $user->province ?: '—' }}
                            </span>
                        </li>

                    </ul>


                    @if($user->id !== auth()->id())

                        <form method="POST"
                              action="{{ route('admin.users.toggleStatus', $user) }}">

                            @csrf
                            @method('PATCH')

                            <button class="btn btn-danger btn-block">

                                <i class="fas fa-power-off mr-1"></i>

                                {{ $user->is_active
                                    ? 'Suspend Account'
                                    : 'Reactivate Account'
                                }}

                            </button>

                        </form>

                    @endif


                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-default btn-block mt-2">

                        Back to Users

                    </a>

                </div>

            </div>

        </div>


        {{-- RIGHT --}}
        <div class="col-lg-8">

            <div class="row">

                <div class="col-md-3">

                    <div class="info-box">

                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </span>

                        <div class="info-box-content">

                            <span class="info-box-text">
                                Orders
                            </span>

                            <span class="info-box-number">
                                {{ $orderStats['total'] }}
                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="info-box">

                        <span class="info-box-icon bg-success">
                            <i class="fas fa-dollar-sign"></i>
                        </span>

                        <div class="info-box-content">

                            <span class="info-box-text">
                                Spent
                            </span>

                            <span class="info-box-number">
                                ${{ number_format($orderStats['spent'], 2) }}
                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="info-box">

                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </span>

                        <div class="info-box-content">

                            <span class="info-box-text">
                                Pending
                            </span>

                            <span class="info-box-number">
                                {{ $orderStats['pending'] }}
                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="info-box">

                        <span class="info-box-icon bg-info">
                            <i class="fas fa-truck"></i>
                        </span>

                        <div class="info-box-content">

                            <span class="info-box-text">
                                Delivered
                            </span>

                            <span class="info-box-number">
                                {{ $orderStats['delivered'] }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ORDERS --}}
            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Recent Orders
                    </h3>

                </div>

                <div class="card-body p-0">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($user->orders->take(8) as $order)

                            <tr>

                                <td>

                                    <a href="{{ route('admin.orders.show', $order) }}">
                                        #{{ $order->order_number }}
                                    </a>

                                </td>

                                <td>
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td>

                                    <span class="badge badge-info">
                                        {{ ucfirst($order->status) }}
                                    </span>

                                </td>

                                <td>
                                    {{ $order->items->count() }}
                                </td>

                                <td class="text-right font-weight-bold">
                                    ${{ number_format($order->total, 2) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-3">
                                    No orders found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- REVIEWS --}}
            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Product Reviews
                    </h3>

                </div>

                <div class="card-body p-0">

                    <table class="table table-hover">

                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Title</th>
                                <th>Date</th>
                            </tr>

                        </thead>

                        <tbody>

                        @forelse($user->reviews->take(5) as $review)

                            <tr>

                                <td>
                                    {{ $review->product->name ?? 'Deleted Item' }}
                                </td>

                                <td>

                                    <span class="text-warning">
                                        {{ $review->rating }} / 5
                                    </span>

                                </td>

                                <td>
                                    {{ $review->title }}
                                </td>

                                <td>
                                    {{ $review->created_at->format('Y-m-d') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="text-center text-muted py-3">
                                    No reviews found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection