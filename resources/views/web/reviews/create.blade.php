@extends('layouts.app')

@section('title', 'Write a Review')


@section('content')

<style>
   /* ── Review Form Page ────────────────────────────────────────── */
.review-form-wrapper {
    max-width: 28rem;
    margin: 0 auto;
    padding: 3rem 1.5rem;
}

.review-form-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}

.review-form-subtitle {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* ── Error Box ───────────────────────────────────────────────── */
.review-form-errors {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
}

.review-form-errors ul {
    list-style: disc;
    padding-left: 1.1rem;
    margin: 0;
}

.review-form-errors li {
    font-size: 0.875rem;
    color: #b91c1c;
}

/* ── Card Wrapper ────────────────────────────────────────────── */
.review-form-card {
    background: #ffffff;
    border: 1px solid #f3f4f6;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 1.75rem;
}

.review-form-group {
    margin-bottom: 1.25rem;
}

.review-form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.4rem;
}

/* ── Inputs ──────────────────────────────────────────────────── */
.review-form-select,
.review-form-input,
.review-form-textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 0.6rem;
    font-size: 0.875rem;
    padding: 0.6rem 0.75rem;
    color: #111827;
    background: #fff;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.review-form-select:focus,
.review-form-input:focus,
.review-form-textarea:focus {
    outline: none;
    border-color: #2952e3;
    box-shadow: 0 0 0 3px rgba(41, 82, 227, 0.12);
}

.review-form-textarea {
    resize: vertical;
    min-height: 100px;
}

/* ── Star Rating Select (optional visual touch) ─────────────── */
.review-form-select {
    cursor: pointer;
}

/* ── File Upload ─────────────────────────────────────────────── */
.review-form-file {
    width: 100%;
    font-size: 0.8rem;
    color: #6b7280;
}

.review-form-file-hint {
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.35rem;
}

/* ── Submit Button ───────────────────────────────────────────── */
.review-form-submit {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 0.6rem;
    border: none;
    background: #2952e3;
    color: #ffffff;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease;
}

.review-form-submit:hover {
    background: #1e3fbf;
}

.review-form-submit:active {
    transform: scale(0.98);
}
</style>
<div class="review-form-wrapper">
    <h1 class="review-form-title">Review {{ $product->name }}</h1>
    <p class="review-form-subtitle">Share your experience with this product</p>

    @if ($errors->any())
        <div class="review-form-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="review-form-card">
        <form action="{{ route('reviews.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderId }}">

            <div class="review-form-group">
                <label class="review-form-label">Rating</label>
                <select name="rating" required class="review-form-select">
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ $i == 5 ? 'selected' : '' }}>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>

            <div class="review-form-group">
                <label class="review-form-label">Title (optional)</label>
                <input type="text" name="title" maxlength="150" class="review-form-input">
            </div>

            <div class="review-form-group">
                <label class="review-form-label">Your review (optional)</label>
                <textarea name="body" maxlength="2000" rows="4" class="review-form-textarea"></textarea>
            </div>

            <div class="review-form-group">
                <label class="review-form-label">Photos (optional, up to 5)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="review-form-file">
                <p class="review-form-file-hint">JPG or PNG, up to 5 images</p>
            </div>

            <button type="submit" class="review-form-submit">
                Submit review
            </button>
        </form>
    </div>
</div>
@endsection