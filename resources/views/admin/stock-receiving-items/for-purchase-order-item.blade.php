@extends('layouts.admin')

@section('title', 'Receiving History')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Receiving History</h1>
        <a href="{{ route('admin.purchase-orders.show', $purchaseOrderItem->purchaseOrder) }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Purchase Order
        </a>
    </div>

    <div class="card card-outline card-info">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-2">Purchase Order</dt>
                <dd class="col-4">{{ $purchaseOrderItem->purchaseOrder->po_number ?? '—' }}</dd>

                <dt class="col-2">Product</dt>
                <dd class="col-4">{{ $purchaseOrderItem->product->name ?? '—' }}</dd>

                <dt class="col-2">Variant</dt>
                <dd class="col-4">{{ $purchaseOrderItem->variant->name ?? '—' }}</dd>

                <dt class="col-2">Qty Ordered</dt>
                <dd class="col-4">{{ $purchaseOrderItem->quantity_ordered }}</dd>

                <dt class="col-2">Qty Received</dt>
                <dd class="col-4">{{ $purchaseOrderItem->quantity_received }}</dd>

                <dt class="col-2">Outstanding</dt>
                <dd class="col-4">
                    @if ($purchaseOrderItem->quantity_outstanding > 0)
                        <span class="badge badge-warning">{{ $purchaseOrderItem->quantity_outstanding }}</span>
                    @else
                        <span class="badge badge-success">0</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Deliveries</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Receiving #</th>
                        <th>Reference</th>
                        <th>Received At</th>
                        <th>Received By</th>
                        <th class="text-right">Qty Received</th>
                        <th class="text-right">Unit Cost</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receivingItems as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.stock-receivings.show', $item->stockReceiving) }}">
                                    #{{ $item->stockReceiving->id }}
                                </a>
                            </td>
                            <td>{{ $item->stockReceiving->reference_number ?? '—' }}</td>
                            <td>{{ $item->stockReceiving->received_at?->format('d M Y H:i') }}</td>
                            <td>{{ $item->stockReceiving->receivedBy->name ?? '—' }}</td>
                            <td class="text-right">{{ $item->quantity_received }}</td>
                            <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.stock-receiving-items.show', $item) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stock has been received against this line item yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
