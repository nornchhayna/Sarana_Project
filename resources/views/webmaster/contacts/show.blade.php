@extends('layouts.webmaster')

@section('title', 'Contact Message')
@section('page-title', 'Contact Message')

@section('content')

<div class="row">

    {{-- =========================
        LEFT: MESSAGE CONTENT
    ========================== --}}
    <div class="col-lg-8">

        {{-- Back --}}
        <div class="mb-3">
            <a href="{{ route('webmaster.contacts.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i>
                Back to Contact Messages
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <h6>
                    <i class="fas fa-exclamation-circle"></i>
                    Please fix the following:
                </h6>

                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- =========================
            CONTACT MESSAGE CARD
        ========================== --}}
        <div class="card card-primary">

            {{-- Header --}}
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-envelope"></i>
                    Contact Message
                </h3>

                <div class="card-tools">

                    @if($contact->status === 'unread')

                        <span class="badge badge-warning">
                            <i class="fas fa-circle"></i>
                            Unread
                        </span>

                    @elseif($contact->status === 'read')

                        <span class="badge badge-secondary">
                            Read
                        </span>

                    @else

                        <span class="badge badge-success">
                            <i class="fas fa-reply"></i>
                            Replied
                        </span>

                    @endif

                </div>
            </div>

            <div class="card-body">

                {{-- Sender --}}
                <div class="d-flex align-items-center mb-4">

                    <div
                        class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:55px;height:55px;font-size:20px;font-weight:600;"
                    >
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>

                    <div>
                        <h4 class="mb-1">
                            {{ $contact->name }}
                        </h4>

                        <div class="text-muted">
                            <i class="fas fa-envelope mr-1"></i>
                            {{ $contact->email }}
                        </div>

                        @if(!empty($contact->phone))
                            <div class="text-muted">
                                <i class="fas fa-phone mr-1"></i>
                                {{ $contact->phone }}
                            </div>
                        @endif
                    </div>

                </div>


                {{-- Subject --}}
                <div class="border-top border-bottom py-3 mb-4">

                    <div class="text-muted text-uppercase small mb-1">
                        Subject
                    </div>

                    <h5 class="mb-0">
                        {{ $contact->subject ?? 'No subject' }}
                    </h5>

                </div>


                {{-- Message --}}
                <div class="mb-4">

                    <div class="text-muted text-uppercase small mb-2">
                        Message
                    </div>

                    <div
                        class="p-3 bg-light rounded"
                        style="white-space:pre-wrap;line-height:1.7;"
                    >
                        {{ $contact->message }}
                    </div>

                </div>


                {{-- Received --}}
                <div class="border-top pt-3 text-muted small">

                    <i class="far fa-clock mr-1"></i>

                    Received
                    {{ $contact->created_at->format('M d, Y \a\t h:i A') }}

                    <span class="ml-1">
                        ({{ $contact->created_at->diffForHumans() }})
                    </span>

                </div>

            </div>

        </div>


        {{-- =========================
            EXISTING REPLY
        ========================== --}}
        @if($contact->status === 'replied' && $contact->reply)

            <div class="card card-success">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-reply"></i>
                        Your Reply

                        @if($contact->replied_at)
                            <small>
                                — sent {{ $contact->replied_at->diffForHumans() }}
                            </small>
                        @endif
                    </h3>

                </div>

                <div class="card-body">

                    <div
                        class="p-3 bg-light rounded"
                        style="white-space:pre-wrap;line-height:1.7;"
                    >
                        {{ $contact->reply }}
                    </div>

                </div>

            </div>

        @endif


        {{-- =========================
            REPLY FORM
        ========================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-paper-plane"></i>

                    {{ $contact->status === 'replied'
                        ? 'Send Another Reply'
                        : 'Reply to ' . $contact->name }}

                </h3>

            </div>

            <form
                action="{{ route('webmaster.contacts.reply', $contact) }}"
                method="POST"
            >

                @csrf

                <div class="card-body">

                    <div class="form-group">

                        <label for="reply">
                            Reply Message
                        </label>

                        <textarea
                            id="reply"
                            name="reply"
                            rows="7"
                            minlength="10"
                            required
                            class="form-control"
                            placeholder="Type your reply here..."
                        >{{ old('reply') }}</textarea>

                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">

                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This will be emailed directly to
                        {{ $contact->email }}
                    </small>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-paper-plane"></i>
                        Send Reply
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================
        RIGHT SIDEBAR
    ========================== --}}
    <div class="col-lg-4">

        {{-- =========================
            CONTACT DETAILS
        ========================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-address-card"></i>
                    Contact Details
                </h3>

            </div>

            <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                    {{-- Name --}}
                    <li class="list-group-item">

                        <div class="text-muted small">
                            <i class="fas fa-user mr-1"></i>
                            Name
                        </div>

                        <strong>
                            {{ $contact->name }}
                        </strong>

                    </li>


                    {{-- Email --}}
                    <li class="list-group-item">

                        <div class="text-muted small">
                            <i class="fas fa-envelope mr-1"></i>
                            Email
                        </div>

                        <a href="mailto:{{ $contact->email }}">
                            {{ $contact->email }}
                        </a>

                    </li>


                    {{-- Phone --}}
                    @if(!empty($contact->phone))

                        <li class="list-group-item">

                            <div class="text-muted small">
                                <i class="fas fa-phone mr-1"></i>
                                Phone
                            </div>

                            <strong>
                                {{ $contact->phone }}
                            </strong>

                        </li>

                    @endif


                    {{-- Status --}}
                    <li class="list-group-item">

                        <div class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Status
                        </div>

                        @if($contact->status === 'unread')

                            <span class="badge badge-warning">
                                Unread
                            </span>

                        @elseif($contact->status === 'read')

                            <span class="badge badge-secondary">
                                Read
                            </span>

                        @else

                            <span class="badge badge-success">
                                Replied
                            </span>

                        @endif

                    </li>


                    {{-- Submitted --}}
                    <li class="list-group-item">

                        <div class="text-muted small">
                            <i class="far fa-clock mr-1"></i>
                            Submitted
                        </div>

                        <strong>
                            {{ $contact->created_at->format('M d, Y h:i A') }}
                        </strong>

                    </li>

                </ul>

            </div>

        </div>


        {{-- =========================
            ACTIONS
        ========================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-cogs"></i>
                    Actions
                </h3>

            </div>

            <div class="card-body">

                {{-- Mark as read --}}
                @if($contact->status !== 'unread')

                    <form
                        action="{{ route('webmaster.contacts.mark-read', $contact) }}"
                        method="POST"
                        class="mb-2"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-default btn-block"
                        >
                            <i class="fas fa-envelope-open"></i>
                            Mark as Read
                        </button>

                    </form>

                @endif


                {{-- Email --}}
                <a
                    href="mailto:{{ $contact->email }}"
                    class="btn btn-primary btn-block mb-2"
                >
                    <i class="fas fa-envelope"></i>
                    Open in Email Client
                </a>


                {{-- Back --}}
                <a
                    href="{{ route('webmaster.contacts.index') }}"
                    class="btn btn-default btn-block mb-2"
                >
                    <i class="fas fa-arrow-left"></i>
                    Back to Messages
                </a>


                {{-- Delete --}}
                <form
                    action="{{ route('webmaster.contacts.destroy', $contact) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this contact message? This cannot be undone.')"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger btn-block"
                    >
                        <i class="fas fa-trash"></i>
                        Delete Message
                    </button>

                </form>

            </div>

        </div>


        {{-- =========================
            MESSAGE STATUS CARD
        ========================== --}}
        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Message Information
                </h3>

            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Message ID</span>
                    <strong>#{{ $contact->id }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status</span>

                    <strong>
                        {{ ucfirst($contact->status) }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span class="text-muted">Received</span>

                    <strong>
                        {{ $contact->created_at->diffForHumans() }}
                    </strong>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection