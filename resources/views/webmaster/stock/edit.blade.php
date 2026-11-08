@extends('layouts.webmaster')

@section('title', 'Adjust Stock SKU')
@section('page-title', 'Adjust Stock Item')

@section('content')

@php
    $isVariant = $isVariant ?? ($stock instanceof \App\Models\ProductVariant);
    $currentQty = $isVariant ? $stock->stock : ($stock->quantity ?? 0);
    $threshold = $stock->low_stock_threshold ?? 5;
    $stockType = $isVariant ? 'variant' : 'product';
    $productObj = $product ?? $stock->product;
@endphp

<div class="row">

    {{-- =====================================================
         LEFT PANEL
    ====================================================== --}}
    <div class="col-md-8">

        <form method="POST"
              action="{{ route('webmaster.stock.update', ['stock' => $stock->id, 'type' => $stockType]) }}">

            @csrf
            @method('PUT')
            <input type="hidden" name="stock_type" value="{{ $stockType }}">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sliders-h mr-2"></i>
                        Base Settings Parameters
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Product --}}
                    <div class="mb-3">

                        <h5 class="font-weight-bold mb-1">
                            {{ $productObj->name ?? 'Product Asset' }}
                        </h5>

                        <div>

                            <span class="text-muted small">
                                SKU:
                                {{ $isVariant ? ($stock->sku ?? $productObj->sku ?? '—') : ($productObj->sku ?? '—') }}
                            </span>

                            @if($isVariant && ($stock->color || $stock->phoneStorage))

                                <span class="text-primary small ml-2">

                                    [Variant:
                                    {{ $stock->color->name ?? '' }}
                                    {{ $stock->phoneStorage->capacity ?? '' }}]

                                </span>
                            @elseif(!$isVariant)
                                <span class="badge badge-secondary ml-2">
                                    Single Product / Standard Item
                                </span>
                            @endif

                        </div>

                    </div>


                    {{-- Current Quantity --}}
                    <div class="form-group">

                        <label for="quantity">
                            Total Current Quantity On-Hand
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            id="quantity"
                            value="{{ old('quantity', $currentQty) }}"
                            class="form-control @error('quantity') is-invalid @enderror"
                            min="0"
                            required
                        >

                        @error('quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Low Stock Threshold --}}
                    <div class="form-group">

                        <label for="low_stock_threshold">
                            Low Stock Safety Trigger Warning Threshold
                        </label>

                        <input
                            type="number"
                            name="low_stock_threshold"
                            id="low_stock_threshold"
                            value="{{ old('low_stock_threshold', $threshold) }}"
                            class="form-control @error('low_stock_threshold') is-invalid @enderror"
                            min="0"
                            required
                        >

                        @error('low_stock_threshold')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Adjustment Note --}}
                    <div class="form-group mb-0">

                        <label for="adjustment_note">
                            Audit Adjustment Log Tracking Note
                        </label>

                        <textarea
                            name="adjustment_note"
                            id="adjustment_note"
                            class="form-control @error('adjustment_note') is-invalid @enderror"
                            rows="4"
                            placeholder="Describe context for manual changes (e.g., Physical cycle count correction, inbound damages item processing...)"
                        >{{ old('adjustment_note') }}</textarea>

                        @error('adjustment_note')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="card-footer">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>
                        Apply Limits

                    </button>

                    <a href="{{ route('webmaster.stock.index') }}"
                       class="btn btn-secondary">

                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Stock

                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- =====================================================
         RIGHT PANEL
    ====================================================== --}}
    <div class="col-md-4">

        <div class="card card-primary">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-calculator mr-2"></i>
                    Quick Increment Adjuster

                </h3>

            </div>


            <div class="card-body">

                {{-- Current Balance --}}
                <div class="info-box bg-light">

                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-boxes"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            On Hand Balance
                        </span>

                        <span class="info-box-number">
                            {{ $currentQty }}
                        </span>

                    </div>

                </div>


                {{-- Warning Line --}}
                <div class="info-box bg-light">

                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Warning Line
                        </span>

                        <span class="info-box-number">
                            ≤ {{ $threshold }}
                        </span>

                    </div>

                </div>


                {{-- Adjustment Form --}}
                <form method="POST"
                      action="{{ route('webmaster.stock.adjust', ['stock' => $stock->id, 'type' => $stockType]) }}">

                    @csrf
                    <input type="hidden" name="stock_type" value="{{ $stockType }}">

                    {{-- Action Type --}}
                    <div class="form-group">

                        <label for="type">
                            Select Action Modifier Type
                        </label>

                        <select
                            name="type"
                            id="type"
                            class="form-control"
                            required>

                            <option value="add">
                                Add (+)
                            </option>

                            <option value="subtract">
                                Subtract (-)
                            </option>

                            <option value="set">
                                Set to (=)
                            </option>

                        </select>

                    </div>


                    {{-- Amount --}}
                    <div class="form-group">

                        <label for="amount">
                            Amount
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            class="form-control"
                            min="0"
                            placeholder="Amount units"
                            required
                        >

                    </div>


                    {{-- Adjustment Note --}}
                    <div class="form-group">

                        <label for="note">
                            Adjustment Note
                        </label>

                        <input
                            type="text"
                            name="note"
                            id="note"
                            class="form-control"
                            placeholder="Inbound batch ID, shelf swap..."
                        >

                    </div>


                    <button type="submit"
                            class="btn btn-primary btn-block">

                        <i class="fas fa-bolt mr-1"></i>
                        Execute Adjustment

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection