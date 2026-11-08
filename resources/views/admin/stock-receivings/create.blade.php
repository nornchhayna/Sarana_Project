@extends('layouts.admin')

@section('title', 'Receive Stock — ' . $purchaseOrder->po_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Receive Stock — {{ $purchaseOrder->po_number }}</h1>
        <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Purchase Order
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($outstandingItems->isEmpty())
        <div class="alert alert-info">
            No items found on this purchase order.
        </div>
    @else
        <form action="{{ route('admin.purchase-orders.receivings.store', $purchaseOrder) }}" method="POST">
            @csrf

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="reference_number">Reference / Delivery Note #</label>
                            <input type="text" name="reference_number" id="reference_number"
                                   value="{{ old('reference_number') }}"
                                   class="form-control @error('reference_number') is-invalid @enderror">
                            @error('reference_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label for="received_at">Received At <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="received_at" id="received_at"
                                   value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('received_at') is-invalid @enderror">
                            @error('received_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label>Supplier</label>
                            <input type="text" value="{{ $purchaseOrder->supplier->name ?? '—' }}" class="form-control" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Outstanding Items</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th class="text-right">Ordered</th>
                                <th class="text-right">Already Received</th>
                                <th class="text-right">Outstanding</th>
                                <th style="width: 140px;">Qty Receiving</th>
                                <th style="width: 160px;">Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($outstandingItems as $item)
                                <tr>
                                    <td>
                                        <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                        <input type="checkbox" class="item-toggle" data-index="{{ $loop->index }}" checked>
                                    </td>
                                    <td>{{ $item->product->name ?? '—' }}</td>
                                    <td>
                                        @if($item->variant)
                                            {{ $item->variant->color->name ?? '' }} / {{ $item->variant->phoneStorage ? ($item->variant->phoneStorage->label ?? $item->variant->phoneStorage->capacity) : '' }} / {{ $item->variant->phoneRam ? ($item->variant->phoneRam->label ?? $item->variant->phoneRam->capacity) : '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $item->quantity_ordered }}</td>
                                    <td class="text-right">{{ $item->quantity_received }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-warning">{{ $item->quantity_outstanding }}</span>
                                    </td>
                                    <td>
                                        <input type="number" min="1" max="{{ $item->quantity_outstanding }}"
                                               name="items[{{ $loop->index }}][quantity_received]"
                                               value="{{ old("items.{$loop->index}.quantity_received", $item->quantity_outstanding) }}"
                                               class="form-control form-control-sm qty-input">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0"
                                               name="items[{{ $loop->index }}][unit_cost]"
                                               value="{{ old("items.{$loop->index}.unit_cost", $item->unit_cost) }}"
                                               class="form-control form-control-sm">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-truck-loading"></i> Record Receiving
                </button>
                <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    @endif
@endsection

@push('js')
<script>
    // Unchecking an item excludes it from submission by disabling its inputs
    // (rather than removing the row), so quantities aren't lost if re-checked.
    document.querySelectorAll('.item-toggle').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const row = checkbox.closest('tr');
            row.querySelectorAll('input:not(.item-toggle)').forEach(function (input) {
                input.disabled = !checkbox.checked;
            });
            row.classList.toggle('text-muted', !checkbox.checked);
        });
    });
</script>
@endpush