@extends('layouts.admin')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">New Blog Post</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.blogs.index') }}">Blogs</a>
                    </li>
                    <li class="breadcrumb-item active">New Post</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-10 offset-md-1">

                <div class="card card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Create Blog Post
                        </h3>
                    </div>

                    <form action="{{ route('admin.blogs.store') }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf

                        <div class="card-body">

                            {{-- Title --}}
                            <div class="form-group">
                                <label for="title">
                                    Title <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Enter blog title"
                                    required
                                >

                                @error('title')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            {{-- Excerpt --}}
                            <div class="form-group">
                                <label for="excerpt">
                                    Excerpt
                                </label>

                                <textarea
                                    name="excerpt"
                                    id="excerpt"
                                    rows="3"
                                    class="form-control @error('excerpt') is-invalid @enderror"
                                    placeholder="Short description of the blog post"
                                >{{ old('excerpt') }}</textarea>

                                @error('excerpt')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            {{-- Content --}}
                            <div class="form-group">
                                <label for="content">
                                    Content <span class="text-danger">*</span>
                                </label>

                                <textarea
                                    name="content"
                                    id="content"
                                    rows="12"
                                    class="form-control @error('content') is-invalid @enderror"
                                    placeholder="Write your blog content here..."
                                    required
                                >{{ old('content') }}</textarea>

                                @error('content')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            {{-- Featured Image --}}
                            <div class="form-group">
                                <label for="image">
                                    Featured Image
                                </label>

                                <div class="custom-file">
                                    <input
                                        type="file"
                                        name="image"
                                        id="image"
                                        accept="image/*"
                                        class="custom-file-input @error('image') is-invalid @enderror"
                                    >

                                    <label class="custom-file-label" for="image">
                                        Choose image
                                    </label>
                                </div>

                                @error('image')
                                    <span class="text-danger small">
                                        {{ $message }}
                                    </span>
                                @enderror

                                <small class="form-text text-muted">
                                    Recommended image format: JPG, JPEG, PNG, WEBP.
                                </small>
                            </div>


                            {{-- Publish Options --}}
                            <div class="row">

                                {{-- Publish Immediately --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input
                                                type="checkbox"
                                                name="is_published"
                                                value="1"
                                                id="is_published"
                                                class="custom-control-input"
                                                {{ old('is_published') ? 'checked' : '' }}
                                            >

                                            <label
                                                class="custom-control-label"
                                                for="is_published"
                                            >
                                                Publish immediately
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                {{-- Publish Date --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="published_at">
                                            Publish Date
                                        </label>

                                        <input
                                            type="date"
                                            name="published_at"
                                            id="published_at"
                                            value="{{ old('published_at') }}"
                                            class="form-control @error('published_at') is-invalid @enderror"
                                        >

                                        @error('published_at')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                        <small class="form-text text-muted">
                                            Optional
                                        </small>
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- Footer --}}
                        <div class="card-footer">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="fas fa-save mr-1"></i>
                                Create Post
                            </button>

                            <a
                                href="{{ route('admin.blogs.index') }}"
                                class="btn btn-secondary"
                            >
                                <i class="fas fa-times mr-1"></i>
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    // Display selected filename
    document.querySelector('.custom-file-input')?.addEventListener('change', function (e) {
        const fileName = e.target.files[0]?.name || 'Choose image';
        const label = e.target.nextElementSibling;

        if (label) {
            label.textContent = fileName;
        }
    });
</script>
@endpush