@extends('layouts.admin')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')

@section('content')

    @include('admin.partials.settings-tabs')

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- Security Settings --}}
        <div class="col-md-8">

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security Configuration
                    </h3>
                </div>

                <form action="{{ route('admin.settings.security.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Two Factor Authentication --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden"
                                       name="two_factor_required"
                                       value="0">

                                <input type="checkbox"
                                       name="two_factor_required"
                                       id="two_factor_required"
                                       value="1"
                                       class="custom-control-input"
                                       @checked(old(
                                           'two_factor_required',
                                           $settings['two_factor_required'] ?? false
                                       ))>

                                <label class="custom-control-label"
                                       for="two_factor_required">
                                    Require two-factor authentication
                                </label>
                            </div>

                            <small class="form-text text-muted ml-4">
                                All admin users will be required to set up
                                2FA on their next login.
                            </small>
                        </div>

                        <hr>

                        {{-- Session Lifetime --}}
                        <div class="form-group">
                            <label for="session_lifetime">
                                <i class="fas fa-clock mr-1"></i>
                                Session Lifetime
                            </label>

                            <div class="input-group">
                                <input type="number"
                                       name="session_lifetime"
                                       id="session_lifetime"
                                       min="5"
                                       max="1440"
                                       value="{{ old(
                                           'session_lifetime',
                                           $settings['session_lifetime'] ?? 120
                                       ) }}"
                                       class="form-control"
                                       required>

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        minutes
                                    </span>
                                </div>
                            </div>

                            <small class="form-text text-muted">
                                How long an administrator can remain
                                inactive before the session expires.
                            </small>

                            @error('session_lifetime')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Minimum Password Length --}}
                        <div class="form-group">
                            <label for="password_min_length">
                                <i class="fas fa-key mr-1"></i>
                                Minimum Password Length
                            </label>

                            <div class="input-group">
                                <input type="number"
                                       name="password_min_length"
                                       id="password_min_length"
                                       min="6"
                                       max="64"
                                       value="{{ old(
                                           'password_min_length',
                                           $settings['password_min_length'] ?? 8
                                       ) }}"
                                       class="form-control"
                                       required>

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        characters
                                    </span>
                                </div>
                            </div>

                            <small class="form-text text-muted">
                                Minimum number of characters required
                                for administrator passwords.
                            </small>

                            @error('password_min_length')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>

        </div>


        {{-- Active Sessions --}}
        <div class="col-md-4">

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-desktop mr-2"></i>
                        Active Sessions
                    </h3>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        Sign out every other session across all devices.
                        Your current session will remain active.
                    </p>

                    <div class="callout callout-info">
                        <h5>
                            <i class="fas fa-info-circle mr-1"></i>
                            Current Session
                        </h5>

                        <p class="mb-0">
                            Your current browser session will not be affected.
                        </p>
                    </div>

                </div>

                <div class="card-footer">

                    <form action="{{ route('admin.settings.security.sessions.destroy') }}"
                          method="POST"
                          onsubmit="return confirm('Log out all other sessions?');">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-outline-danger btn-block">

                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Log Out Other Sessions

                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>

@endsection