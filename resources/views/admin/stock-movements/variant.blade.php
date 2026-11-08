@extends('layouts.admin')

@section('title', 'Stock Movements — ' . ($variant->product->name ?? 'Variant'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">
            Stock Movements — {{ $variant->product->name ?? 'Unknown Product' }}
            @if ($variant->name)
                <small class="text-muted">({{ $variant->name }})</small>
            @endif
        </h1>
        <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> All Movements
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Ledger</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-right">Quantity</th>
                        <th>Reference</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at?->format('d M Y H:i') }}</td>
                            <td>
                                @php
                                    $isInbound = str_ends_with($movement->type, '_in') || $movement->type === 'purchase_receipt' || $movement->type === 'return';
                                @endphp
                                <span class="badge badge-{{ $isInbound ? 'success' : 'danger' }}">
                                    {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                {{ $isInbound ? '+' : '−' }}{{ $movement->quantity }}
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ ucfirst(str_replace('_', ' ', $movement->reference_type ?? '—')) }}
                                    @if ($movement->reference_id)
                                        #{{ $movement->reference_id }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($movement->notes, 40) ?: '—' }}</td>
                            <td>{{ $movement->createdBy->name ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.stock-movements.show', $movement) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stock movements recorded for this variant yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
