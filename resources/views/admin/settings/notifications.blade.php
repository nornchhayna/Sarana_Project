@extends('layouts.admin')

@section('title', 'Notification Settings')
@section('page-title', 'Notification Settings')

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

        {{-- Notification Configuration --}}
        <div class="col-md-9">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell mr-2"></i>
                        Notification Preferences
                    </h3>
                </div>

                <form action="{{ route('admin.settings.notifications.update') }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th style="width: 55%;">
                                            Notification
                                        </th>

                                        <th class="text-center">
                                            <i class="fas fa-envelope mr-1"></i>
                                            Email
                                        </th>

                                        <th class="text-center">
                                            <i class="fas fa-sms mr-1"></i>
                                            SMS
                                        </th>

                                        <th class="text-center">
                                            <i class="fas fa-mobile-alt mr-1"></i>
                                            Push
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($notifications as $notification)

                                        <tr>

                                            {{-- Notification --}}
                                            <td>

                                                <strong>
                                                    {{ $notification->label }}
                                                </strong>

                                                <br>

                                                <small class="text-muted">
                                                    <code>
                                                        {{ $notification->key }}
                                                    </code>
                                                </small>

                                            </td>


                                            {{-- Email --}}
                                            <td class="text-center align-middle">

                                                <input type="hidden"
                                                       name="notifications[{{ $notification->id }}][email_enabled]"
                                                       value="0">

                                                <div class="custom-control custom-checkbox d-inline-block">

                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="email_{{ $notification->id }}"
                                                           name="notifications[{{ $notification->id }}][email_enabled]"
                                                           value="1"
                                                           @checked($notification->email_enabled)>

                                                    <label class="custom-control-label"
                                                           for="email_{{ $notification->id }}">
                                                    </label>

                                                </div>

                                            </td>


                                            {{-- SMS --}}
                                            <td class="text-center align-middle">

                                                <input type="hidden"
                                                       name="notifications[{{ $notification->id }}][sms_enabled]"
                                                       value="0">

                                                <div class="custom-control custom-checkbox d-inline-block">

                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="sms_{{ $notification->id }}"
                                                           name="notifications[{{ $notification->id }}][sms_enabled]"
                                                           value="1"
                                                           @checked($notification->sms_enabled)>

                                                    <label class="custom-control-label"
                                                           for="sms_{{ $notification->id }}">
                                                    </label>

                                                </div>

                                            </td>


                                            {{-- Push --}}
                                            <td class="text-center align-middle">

                                                <input type="hidden"
                                                       name="notifications[{{ $notification->id }}][push_enabled]"
                                                       value="0">

                                                <div class="custom-control custom-checkbox d-inline-block">

                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="push_{{ $notification->id }}"
                                                           name="notifications[{{ $notification->id }}][push_enabled]"
                                                           value="1"
                                                           @checked($notification->push_enabled)>

                                                    <label class="custom-control-label"
                                                           for="push_{{ $notification->id }}">
                                                    </label>

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="4"
                                                class="text-center py-5">

                                                <div class="text-muted">

                                                    <i class="far fa-bell-slash fa-2x mb-3"></i>

                                                    <p class="mb-0">
                                                        No notification types
                                                        configured yet.
                                                    </p>

                                                </div>

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- Footer --}}
                    @if ($notifications->isNotEmpty())

                        <div class="card-footer">

                            <button type="submit"
                                    class="btn btn-primary">

                                <i class="fas fa-save mr-1"></i>
                                Save Changes

                            </button>

                        </div>

                    @endif

                </form>

            </div>

        </div>


        {{-- Information --}}
        <div class="col-md-3">

            <div class="card card-info">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        About Notifications
                    </h3>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        Choose which channels are enabled for each
                        notification type.
                    </p>

                    <ul class="pl-3 mb-0">

                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary mr-1"></i>
                            <strong>Email</strong>
                            notifications
                        </li>

                        <li class="mb-2">
                            <i class="fas fa-comment-alt text-primary mr-1"></i>
                            <strong>SMS</strong>
                            notifications
                        </li>

                        <li>
                            <i class="fas fa-mobile-alt text-primary mr-1"></i>
                            <strong>Push</strong>
                            notifications
                        </li>

                    </ul>

                </div>

            </div>


            {{-- Example --}}
            <div class="card card-default">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Tip
                    </h3>
                </div>

                <div class="card-body">

                    <small class="text-muted">
                        Enable Email notifications for important
                        events such as new orders, payment confirmation,
                        shipping updates, and low stock alerts.
                    </small>

                </div>

            </div>

        </div>

    </div>

@endsection