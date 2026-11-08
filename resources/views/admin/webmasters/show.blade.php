@extends('layouts.admin')

@section('title', 'Webmaster Dossier')
@section('page-title', 'Webmaster Profile — ' . $webmaster->name)

@section('content')

<div class="row">
    {{-- PROFILE CARD --}}
    <div class="col-lg-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <div class="bg-warning text-dark rounded-circle mx-auto d-flex align-items-center justify-content-center font-weight-bold mb-3"
                     style="width: 90px; height: 90px; font-size: 32px;">
                    {{ strtoupper(substr($webmaster->name, 0, 2)) }}
                </div>

                <h3 class="profile-username font-weight-bold mb-1">
                    {{ $webmaster->name }}
                </h3>

                <p class="text-muted mb-2">
                    <span class="badge badge-warning text-dark px-2 py-1">
                        <i class="fas fa-user-shield mr-1"></i> Webmaster
                    </span>
                    @if($webmaster->is_active)
                        <span class="badge badge-success px-2 py-1 ml-1">
                            <i class="fas fa-check-circle mr-1"></i> Active
                        </span>
                    @else
                        <span class="badge badge-danger px-2 py-1 ml-1">
                            <i class="fas fa-ban mr-1"></i> Suspended
                        </span>
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3 text-left">
                    <li class="list-group-item">
                        <b>Email</b>
                        <span class="float-right text-muted">{{ $webmaster->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b>
                        <span class="float-right text-muted">{{ $webmaster->phone ?: '—' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Gender</b>
                        <span class="float-right text-muted">{{ $webmaster->gender ? ucfirst($webmaster->gender) : '—' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>City</b>
                        <span class="float-right text-muted">{{ $webmaster->city ?: '—' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Province</b>
                        <span class="float-right text-muted">{{ $webmaster->province ?: '—' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Address</b>
                        <span class="float-right text-muted">{{ $webmaster->address ?: '—' }}</span>
                    </li>
                </ul>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.webmasters.edit', $webmaster) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i> Edit Profile
                    </a>

                    @if($webmaster->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.webmasters.toggleStatus', $webmaster) }}" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-block {{ $webmaster->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                    onclick="return confirm('Change status for webmaster &quot;{{ $webmaster->name }}&quot;?')">
                                <i class="fas {{ $webmaster->is_active ? 'fa-user-slash' : 'fa-user-check' }} mr-1"></i>
                                {{ $webmaster->is_active ? 'Suspend Account' : 'Activate Account' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.webmasters.destroy', $webmaster) }}" class="mb-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-block"
                                    onclick="return confirm('Permanently delete webmaster &quot;{{ $webmaster->name }}&quot;? This cannot be undone.')">
                                <i class="fas fa-trash mr-1"></i> Delete Webmaster
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.webmasters.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Webmasters
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILS & SYSTEM ACCESS --}}
    <div class="col-lg-8">
        {{-- Account Meta Card --}}
        <div class="card card-primary card-outline mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i> Account Details & Timestamps
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">Account ID</div>
                        <div class="font-weight-bold">#{{ $webmaster->id }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">System Role</div>
                        <div class="font-weight-bold text-warning">{{ ucfirst($webmaster->role) }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">Registered On</div>
                        <div class="font-weight-bold">
                            {{ $webmaster->created_at ? $webmaster->created_at->format('F d, Y h:i A') : '—' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">Last Profile Update</div>
                        <div class="font-weight-bold">
                            {{ $webmaster->updated_at ? $webmaster->updated_at->format('F d, Y h:i A') : '—' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">Email Verification</div>
                        <div class="font-weight-bold">
                            @if($webmaster->email_verified_at)
                                <span class="text-success"><i class="fas fa-check mr-1"></i> Verified ({{ $webmaster->email_verified_at->format('M d, Y') }})</span>
                            @else
                                <span class="text-muted"><i class="fas fa-clock mr-1"></i> Not verified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted text-sm">Portal Access URL</div>
                        <div class="font-weight-bold">
                            <code>{{ url('/webmaster') }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permitted Modules in Webmaster Panel --}}
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i> Webmaster Accessible Modules
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    As a Webmaster, this user has operational authority in the Ashahi store to manage:
                </p>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-box text-primary mr-2 fa-lg"></i>
                            <div>
                                <strong>Products & Inventory</strong>
                                <div class="text-muted text-sm">Create, edit products, manage stock levels</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-folder-open text-info mr-2 fa-lg"></i>
                            <div>
                                <strong>Catalog Taxonomy</strong>
                                <div class="text-muted text-sm">Categories, Brands, Models, Colors, Storage</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shopping-bag text-success mr-2 fa-lg"></i>
                            <div>
                                <strong>Orders & Invoices</strong>
                                <div class="text-muted text-sm">Order fulfillment, status updates, invoices</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bullhorn text-warning mr-2 fa-lg"></i>
                            <div>
                                <strong>Marketing & Sliders</strong>
                                <div class="text-muted text-sm">Promotional codes, homepage sliders</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-comments text-purple mr-2 fa-lg"></i>
                            <div>
                                <strong>Reviews & Inquiries</strong>
                                <div class="text-muted text-sm">Review moderation and customer contact replies</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card text-danger mr-2 fa-lg"></i>
                            <div>
                                <strong>Payment Verification</strong>
                                <div class="text-muted text-sm">Verify KHQR and order payments</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
