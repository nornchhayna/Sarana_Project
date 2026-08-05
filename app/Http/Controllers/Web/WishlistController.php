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

    public function index()
    {
        $wishlists = Wishlist::with(['product.brand', 'product.stock', 'color', 'phoneStorage'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('web.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $colorId = $request->input('color_id');
        $storageId = $request->input('phone_storage_id');

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('color_id', $colorId)
            ->where('phone_storage_id', $storageId)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'color_id' => $colorId,
                'phone_storage_id' => $storageId,
            ]);
            $wishlisted = true;
            $message = 'Added to wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json(['wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'phone_storage_id' => ['nullable', 'exists:phone_storages,id'],
        ]);

      $wishlist = Wishlist::updateOrCreate(
    [
        'user_id' => auth()->id(),
        'product_id' => $validated['product_id'],
    ],
    [
        'color_id' => $validated['color_id'] ?? null,
        'phone_storage_id' => $validated['phone_storage_id'] ?? null,
    ]
);

        if ($request->wantsJson()) {
            return response()->json([
                'wishlisted' => true,
                'message' => 'Added to wishlist.',
            ]);
        }

        return back()->with('success', 'Added to wishlist.');
    }

    // Deletes a single wishlist entry by its own id, so removing one
    // variant doesn't wipe out other variants of the same product that
    // are also on the wishlist. Update your route to bind {wishlist}
    // instead of {product} — see note below.
    public function destroy(Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === auth()->id(), 403);

        $wishlist->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}