<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── View wishlist ──────────────────────────────────────────────────────

    public function index()
    {
        $wishlists = Wishlist::with(['product.brand', 'product.stock'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('web.wishlist.index', compact('wishlists'));
    }

    // ─── Toggle wishlist (add / remove) ────────────────────────────────────

    public function toggle(Request $request, Product $product)
    {
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $wishlisted = true;
            $message = 'Added to wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json(['wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    // ─── Remove a specific wishlist item ────────────────────────────────────

    public function destroy(Product $product)
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}
