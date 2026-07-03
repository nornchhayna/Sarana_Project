<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Submit a review ────────────────────────────────────────────────────

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'body' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ]);

        // Ensure the customer actually purchased this product (if order is provided)
        if (! empty($validated['order_id'])) {
            $purchased = Order::where('id', $validated['order_id'])
                ->where('user_id', auth()->id())
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();

            if (! $purchased) {
                return back()->with('error', 'You can only review products you have purchased.');
            }
        }

        // Prevent duplicate review for same product + order combination
        $alreadyReviewed = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('order_id', $validated['order_id'] ?? null)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Upload review images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('review-images', 'public');
            }
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'order_id' => $validated['order_id'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'] ?? null,
            'images' => ! empty($imagePaths) ? $imagePaths : null,
            'is_approved' => false, // pending admin approval
        ]);
        // Ensure the customer actually purchased this product (if order is provided)
if (! empty($validated['order_id'])) {
    $order = Order::where('id', $validated['order_id'])
        ->where('user_id', auth()->id())
        ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
        ->first();

    if (! $order) {
        return back()->with('error', 'You can only review products you have purchased.');
    }

    if ($order->status !== 'delivered') {
        return back()->with('error', 'You can review this product once your order has been delivered.');
    }
}

        // Recalculate avg_rating for the product
        $this->updateProductRating($product);

        return back()->with('success', 'Thank you for your review! It will be visible after approval.');
    }

    // ─── Edit review form ───────────────────────────────────────────────────

    public function edit(Review $review)
    {
        abort_if($review->user_id !== auth()->id(), 403);

        return view('web.reviews.edit', compact('review'));
    }

    // ─── Update a review ────────────────────────────────────────────────────

    public function update(Request $request, Review $review)
    {
        abort_if($review->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'body' => 'nullable|string|max:2000',
        ]);

        $review->update(array_merge($validated, ['is_approved' => false]));

        $this->updateProductRating($review->product);

        return redirect()->route('products.show', $review->product->slug)
            ->with('success', 'Review updated and pending re-approval.');
    }

    // ─── Delete a review ────────────────────────────────────────────────────

    public function destroy(Review $review)
    {
        abort_if($review->user_id !== auth()->id(), 403);

        $product = $review->product;
        $review->delete();

        $this->updateProductRating($product);

        return back()->with('success', 'Review deleted.');
    }

    // ─── Helper: recalculate product avg_rating ─────────────────────────────

    private function updateProductRating(Product $product): void
    {
        $avg = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $product->update(['avg_rating' => round($avg, 2)]);
    }
}
