@extends('layouts.admin')

@section('title', 'Settings')
@section('subtitle', 'Configure how your store operates')

@section('content')

    {{-- Settings Tabs --}}
    @include('admin.partials.settings-tabs')

    <div class="row">

        {{-- General --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.general') }}" class="text-decoration-none">
                <div class="card card-primary card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-cog fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">General</h5>
                                <small class="text-muted">Basic website configuration</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Site name, logo, favicon, language, and timezone.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Store --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.store') }}" class="text-decoration-none">
                <div class="card card-success card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-store fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Store</h5>
                                <small class="text-muted">Store information</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Contact email, phone number, address, and business information.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Payment --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.payment') }}" class="text-decoration-none">
                <div class="card card-warning card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-credit-card fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Payment</h5>
                                <small class="text-muted">Payment configuration</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Currency, payment methods, gateways, and transaction settings.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Shipping --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.shipping') }}" class="text-decoration-none">
                <div class="card card-info card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-truck fa-2x text-info"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Shipping</h5>
                                <small class="text-muted">Delivery configuration</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Shipping methods, flat rates, delivery zones, and free-shipping threshold.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Notifications --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.notifications') }}" class="text-decoration-none">
                <div class="card card-danger card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-bell fa-2x text-danger"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Notifications</h5>
                                <small class="text-muted">Communication channels</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Configure email, SMS, push notifications, and order alerts.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Security --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.settings.security') }}" class="text-decoration-none">
                <div class="card card-secondary card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-shield-alt fa-2x text-secondary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Security</h5>
                                <small class="text-muted">Account security</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Two-factor authentication, sessions, password policies, and security options.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Database & Backups --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.backups.index') }}" class="text-decoration-none">
                <div class="card card-dark card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-database fa-2x text-dark"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Backups</h5>
                                <small class="text-muted">System & database backup</small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            Create database dumps, archive media assets, download, and restore backups.
                        </p>
                    </div>
                </div>
            </a>
        </div>

    </div>

@endsection