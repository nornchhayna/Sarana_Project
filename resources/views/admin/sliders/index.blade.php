@extends('layouts.admin')

@section('title', 'Sliders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Sliders</h1>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Slider
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <table class="table table-bordered align-middle" id="sliders-table">
            <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th style="width: 150px;">Image</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Link</th>
                    <th style="width: 100px;">Order</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-sliders">
                @forelse ($sliders as $slider)
                    <tr data-id="{{ $slider->id }}">
                        <td class="text-center" style="cursor: move;">
                            <i class="fas fa-grip-vertical text-muted"></i>
                        </td>
                        <td>
                            <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="img-thumbnail" style="max-height: 60px;">
                        </td>
                        <td>{{ $slider->title ?? '-' }}</td>
                        <td>{{ Str::limit($slider->subtitle, 50) ?? '-' }}</td>
                        <td>
                            @if ($slider->link)
                                <a href="{{ $slider->link }}" target="_blank">{{ Str::limit($slider->link, 30) }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $slider->sort_order }}</td>
                        <td>
                          <form action="{{ route('admin.sliders.toggleStatus', $slider) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm @if($slider->is_active) btn-success @else btn-secondary @endif">
                                    {{ $slider->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No sliders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('sortable-sliders');
        if (!el) return;

        new Sortable(el, {
            handle: '.fas.fa-grip-vertical',
            animation: 150,
            onEnd: function () {
                const order = Array.from(el.querySelectorAll('tr')).map(row => row.dataset.id);

                fetch('{{ route('admin.sliders.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ order: order }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        order.forEach((id, index) => {
                            const row = el.querySelector(`tr[data-id="${id}"]`);
                            row.querySelector('td:nth-child(6)').textContent = index;
                        });
                    }
                });
            }
        });
    });
</script>
@endpush