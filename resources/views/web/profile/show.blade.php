@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="profile-page">
    <div class="profile-card">

        {{-- GRADIENT BANNER --}}
        <div class="profile-banner"></div>

        {{-- AVATAR + NAME + EDIT --}}
        <div class="profile-header">

            <div class="profile-identity">
                @php
                    $imgPath = $user->avatar ?? 
                               ($user->images ? json_decode($user->images, true)[0] ?? null : null);
                @endphp
                <img src="{{ $imgPath ? asset('storage/'.$imgPath) : asset('default-avatar.png') }}"
                     class="profile-avatar" alt="{{ $user->name }}">
                <div>
                    <h4 class="profile-name">{{ $user->name }}</h4>
                    <p class="profile-email">{{ $user->email }}</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn-edit">Edit</a>
        </div>

        {{-- FORM FIELDS --}}
        <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Your First Name">
                </div>
                <div class="form-field">
                    <label>Nick Name</label>
                    <input type="text" name="nickname" value="{{ old('nickname', $user->nickname ?? '') }}" placeholder="Your First Name">
                </div>

                <div class="form-field">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="" disabled {{ !($user->gender ?? false) ? 'selected' : '' }}>Your First Name</option>
                        <option value="male" {{ ($user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ ($user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ ($user->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Country</label>
                    <select name="country">
                        <option value="" disabled {{ !($user->country ?? false) ? 'selected' : '' }}>Your First Name</option>
                        @foreach(($countries ?? []) as $code => $countryName)
                            <option value="{{ $code }}" {{ ($user->country ?? '') == $code ? 'selected' : '' }}>{{ $countryName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label>Language</label>
                    <select name="language">
                        <option value="" disabled {{ !($user->language ?? false) ? 'selected' : '' }}>Your First Name</option>
                        @foreach(($languages ?? []) as $code => $languageName)
                            <option value="{{ $code }}" {{ ($user->language ?? '') == $code ? 'selected' : '' }}>{{ $languageName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label>Time Zone</label>
                    <select name="timezone">
                        <option value="" disabled {{ !($user->timezone ?? false) ? 'selected' : '' }}>Your First Name</option>
                        @foreach(($timezones ?? []) as $tz)
                            <option value="{{ $tz }}" {{ ($user->timezone ?? '') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        {{-- EMAIL ADDRESS SECTION --}}
        <div class="email-section">
            <h5 class="email-heading">My email Address</h5>

            @forelse(($user->emails ?? [$user]) as $emailEntry)
                <div class="email-row">
                    <span class="email-icon">✉</span>
                    <div>
                        <div class="email-value">{{ $emailEntry->email ?? $user->email }}</div>
                        <div class="email-meta">{{ isset($emailEntry->created_at) ? $emailEntry->created_at->diffForHumans() : '' }}</div>
                    </div>
                </div>
            @empty
            @endforelse

            <button type="button" class="btn-add-email" onclick="document.getElementById('addEmailForm').classList.toggle('d-none')">
                + Add Email Address
            </button>

      
        </div>

    </div>
</div>

<style>
.profile-page { padding: 40px; }
.profile-card {
    background: #fff; border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}

.profile-banner {
    height: 90px;
    background: linear-gradient(90deg, #b7d3f7 0%, #d9d3ef 45%, #f3e3c8 100%);
}

.profile-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 32px; margin-top: -34px;
}
.profile-identity { display: flex; align-items: center; gap: 16px; }
.profile-avatar {
    width: 88px; height: 88px; border-radius: 50%; object-fit: cover;
    border: 4px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.profile-name { margin: 0; font-size: 20px; font-weight: 700; color: #253D4E; }
.profile-email { margin: 2px 0 0; color: #9aa0a6; font-size: 14px; }

.btn-edit {
    background: #3f6fee; color: #fff; border: none; border-radius: 8px;
    padding: 10px 26px; font-weight: 600; font-size: 14px; text-decoration: none;
    display: inline-block; cursor: pointer; align-self: flex-start; margin-top: 40px;
}
.btn-edit:hover { background: #3260d8; color: #fff; }

.profile-form { padding: 32px; padding-top: 24px; }
.form-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 24px 32px;
}
.form-field label {
    display: block; font-size: 14px; font-weight: 600; color: #253D4E; margin-bottom: 8px;
}
.form-field input,
.form-field select {
    width: 100%; background: #f4f5f7; border: none; border-radius: 8px;
    padding: 14px 16px; font-size: 14px; color: #6b7280;
    appearance: none; -webkit-appearance: none;
}
.form-field select {
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'><path d='M1 1l5 5 5-5' stroke='%236b7280' stroke-width='1.5' fill='none' fill-rule='evenodd'/></svg>");
    background-repeat: no-repeat;
    background-position: right 16px center;
}

.email-section { padding: 0 32px 32px; }
.email-heading { font-size: 17px; font-weight: 700; color: #253D4E; margin: 8px 0 20px; }
.email-row { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.email-icon {
    width: 36px; height: 36px; border-radius: 50%; background: #e9efff;
    color: #3f6fee; display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.email-value { font-weight: 600; color: #253D4E; font-size: 14px; }
.email-meta { color: #9aa0a6; font-size: 12px; margin-top: 2px; }

.btn-add-email {
    background: #e9efff; color: #3f6fee; border: none; border-radius: 8px;
    padding: 10px 22px; font-weight: 600; font-size: 14px; cursor: pointer;
}
.btn-add-email:hover { background: #dbe6ff; }

.add-email-form { display: flex; gap: 10px; margin-top: 16px; }
.add-email-form input {
    flex: 1; border: 1px solid #e2e2e2; border-radius: 8px; padding: 10px 14px; font-size: 14px;
}
.d-none { display: none; }
</style>
@endsection