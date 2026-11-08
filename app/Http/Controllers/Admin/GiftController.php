<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    /**
     * Display a listing of gifts.
     */
    public function index()
    {
        $gifts = Gift::latest()->paginate(10);

        return view('admin.gifts.index', compact('gifts'));
    }

    /**
     * Show the form for creating a new gift.
     */
    public function create()
    {
        return view('admin.gifts.create');
    }

    /**
     * Store a newly created gift.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('gifts', 'public');
        }

        Gift::create($validated);

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift created successfully.');
    }

    /**
     * Show the form for editing the specified gift.
     */
    public function edit(Gift $gift)
    {
        return view('admin.gifts.edit', compact('gift'));
    }

    /**
     * Update the specified gift.
     */
    public function update(Request $request, Gift $gift)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($gift->image) {
                Storage::disk('public')->delete($gift->image);
            }
            $validated['image'] = $request->file('image')->store('gifts', 'public');
        }

        $gift->update($validated);

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift updated successfully.');
    }

    /**
     * Remove the specified gift.
     */
    public function destroy(Gift $gift)
    {
        if ($gift->image) {
            Storage::disk('public')->delete($gift->image);
        }

        $gift->delete();

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift deleted successfully.');
    }
}
