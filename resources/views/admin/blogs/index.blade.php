@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">All Posts</h3>
            <div class="card-tools">
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> New Post
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blogs as $blog)
                        <tr>
                            <td>
                                @if ($blog->image)
                                    <img src="{{ Storage::url($blog->image) }}" alt="{{ $blog->title }}"
                                         class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded"
                                         style="width: 56px; height: 56px; font-size: 10px;">
                                        No image
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $blog->title }}</div>
                                <div class="small text-muted">{{ $blog->slug }}</div>
                            </td>
                            <td>
                                @if ($blog->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-muted">
                                {{ $blog->published_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="text-primary small font-weight-bold">Edit</a>
                                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                                          onsubmit="return confirm('Delete this post?');" class="mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger small font-weight-bold p-0 border-0">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No blog posts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $blogs->links() }}
        </div>
    </div>

@endsection