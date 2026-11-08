@extends('layouts.admin')

@section('title', 'General Settings')
@section('subtitle', 'Configure your general store settings')

@section('content')

    {{-- Settings Navigation --}}
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

        <div class="col-lg-8">

            <form action="{{ route('admin.settings.general.update') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- General Settings Card --}}
                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog mr-2"></i>
                            General Settings
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Site Name --}}
                        <div class="form-group">
                            <label for="site_name">
                                Site Name
                            </label>

                            <input
                                type="text"
                                name="site_name"
                                id="site_name"
                                value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                                class="form-control @error('site_name') is-invalid @enderror"
                                placeholder="Enter site name"
                            >

                            @error('site_name')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>


                        {{-- Site Logo --}}
                        <div class="form-group">
                            <label for="site_logo">
                                Site Logo
                            </label>

                            @if (!empty($settings['site_logo']))

                                <div class="mb-3">
                                    <p class="text-muted mb-2">
                                        Current Logo
                                    </p>

                                    <div class="border rounded p-2 d-inline-block">
                                        <img
                                            src="{{ Storage::url($settings['site_logo']) }}"
                                            alt="Current logo"
                                            style="max-height: 80px; max-width: 200px;"
                                            class="img-fluid"
                                        >
                                    </div>
                                </div>

                            @endif

                            <div class="custom-file">
                                <input
                                    type="file"
                                    name="site_logo"
                                    id="site_logo"
                                    class="custom-file-input @error('site_logo') is-invalid @enderror"
                                    accept="image/png,image/jpeg,image/jpg"
                                >

                                <label class="custom-file-label" for="site_logo">
                                    Choose logo
                                </label>
                            </div>

                            @error('site_logo')
                                <span class="text-danger d-block mt-1">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                PNG or JPG, maximum 2MB.
                            </small>
                        </div>


                        {{-- Timezone --}}
                        <div class="form-group mb-0">
                            <label for="timezone">
                                Timezone
                            </label>

                            <select
                                name="timezone"
                                id="timezone"
                                class="form-control @error('timezone') is-invalid @enderror"
                            >
                                @foreach (timezone_identifiers_list() as $tz)

                                    <option
                                        value="{{ $tz }}"
                                        @selected(
                                            old(
                                                'timezone',
                                                $settings['timezone'] ?? 'UTC'
                                            ) === $tz
                                        )
                                    >
                                        {{ $tz }}
                                    </option>

                                @endforeach
                            </select>

                            @error('timezone')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Select the timezone used by your store.
                            </small>
                        </div>

                        {{-- Site Description --}}
                        <div class="form-group mt-3 mb-0">
                            <label for="site_description">
                                Store Tagline / Description
                            </label>

                            <textarea
                                name="site_description"
                                id="site_description"
                                rows="3"
                                class="form-control @error('site_description') is-invalid @enderror"
                                placeholder="A brief description or tagline of your store"
                            >{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>

                            @error('site_description')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Appears in meta tags and branding sections.
                            </small>
                        </div>

                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer d-flex justify-content-end">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-save mr-1"></i>
                            Save Changes
                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- Right Information Panel --}}
        <div class="col-lg-4">

            <div class="card card-info card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        About General Settings
                    </h3>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        These settings control the basic information
                        used throughout your store.
                    </p>

                    <ul class="pl-3 mb-0">

                        <li class="mb-2">
                            <strong>Site Name</strong><br>
                            <small class="text-muted">
                                Displayed in your website title and branding.
                            </small>
                        </li>

                        <li class="mb-2">
                            <strong>Site Logo</strong><br>
                            <small class="text-muted">
                                Used for your store branding.
                            </small>
                        </li>

                        <li>
                            <strong>Timezone</strong><br>
                            <small class="text-muted">
                                Used for orders, reports, and system dates.
                            </small>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
<script>
    // Show selected filename for AdminLTE custom file input
    document.addEventListener('change', function (event) {
        if (event.target.classList.contains('custom-file-input')) {
            const fileName = event.target.files[0]?.name || 'Choose logo';
            const label = event.target.nextElementSibling;

            if (label) {
                label.textContent = fileName;
            }
        }
    });
</script>
@endpush