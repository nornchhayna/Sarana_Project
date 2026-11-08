<?php

namespace App\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebmasterReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->search.'%'))
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
            'avg' => round(Review::where('is_approved', true)->avg('rating'), 1),
        ];

        return view('webmaster.reviews.index', compact('reviews', 'stats'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product', 'order']);

        return view('webmaster.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        $avg = Review::where('product_id', $review->product_id)
            ->where('is_approved', true)
            ->avg('rating');

        $review->product->update(['avg_rating' => round($avg, 2)]);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        $avg = Review::where('product_id', $review->product_id)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $review->product->update(['avg_rating' => round($avg, 2)]);

        return back()->with('success', 'Review rejected.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:reviews,id']);

        Review::whereIn('id', $request->ids)->update(['is_approved' => true]);

        return back()->with('success', count($request->ids).' reviews approved.');
    }

    public function destroy(Review $review)
    {
        foreach ($review->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }

        $productId = $review->product_id;
        $review->delete();

        $avg = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        Product::where('id', $productId)->update(['avg_rating' => round($avg, 2)]);

        return redirect()->route('webmaster.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
