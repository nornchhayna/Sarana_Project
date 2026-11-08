<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewApiController extends Controller
{
    public function index(string $productIdOrSlug): JsonResponse
    {
        $product = Product::where('slug', $productIdOrSlug)
            ->when(is_numeric($productIdOrSlug), fn ($q) => $q->orWhere('id', (int) $productIdOrSlug))
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->latest()
            ->get()
            ->map(fn (Review $r) => $this->transformReview($r))
            ->values();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'data' => $reviews,
            'average_rating' => round((float) ($product->reviews()->where('is_approved', true)->avg('rating') ?? 0), 1),
            'total_reviews' => $reviews->count(),
        ]);
    }

    public function store(Request $request, $productId): JsonResponse
    {
        $product = Product::where('id', $productId)
            ->orWhere('slug', $productId)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'body' => 'nullable|string|max:2000',
            'comment' => 'nullable|string|max:2000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $body = $validated['body'] ?? $validated['comment'] ?? null;

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'order_id' => $validated['order_id'] ?? null,
            ],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'body' => $body,
                'is_approved' => true,
            ]
        );

        $this->recalculateProductRating($product);
        $review->load('user');
        $reviewData = $this->transformReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $reviewData,
            'data' => $reviewData,
        ], 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        abort_if($review->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'body' => 'nullable|string|max:2000',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? $review->title,
            'body' => $validated['body'] ?? $validated['comment'] ?? $review->body,
            'is_approved' => true,
        ]);

        if ($review->product) {
            $this->recalculateProductRating($review->product);
        }

        $review->load('user');
        $reviewData = $this->transformReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'review' => $reviewData,
            'data' => $reviewData,
        ]);
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        abort_if($review->user_id !== $request->user()->id, 403);

        $product = $review->product;
        $review->delete();

        if ($product) {
            $this->recalculateProductRating($product);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }

    private function recalculateProductRating(Product $product): void
    {
        $avg = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $product->update(['avg_rating' => round((float) $avg, 2)]);
    }

    private function transformReview(Review $review): array
    {
        return [
            'id' => $review->id,
            'product_id' => $review->product_id,
            'user_id' => $review->user_id,
            'order_id' => $review->order_id,
            'rating' => (int) $review->rating,
            'title' => $review->title,
            'body' => $review->body,
            'comment' => $review->body,
            'images' => $review->images ?? [],
            'is_approved' => (bool) $review->is_approved,
            'user' => $review->user ? [
                'id' => $review->user->id,
                'first_name' => $review->user->first_name,
                'last_name' => $review->user->last_name,
                'name' => $review->user->name,
                'avatar' => $review->user->avatar,
                'avatar_url' => $review->user->avatar_url,
            ] : null,
            'created_at' => $review->created_at?->toIso8601String(),
            'created_at_human' => $review->created_at?->diffForHumans(),
        ];
    }
}
