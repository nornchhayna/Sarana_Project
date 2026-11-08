<?php

namespace App\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Validation\Rule;

class WebmasterColorController extends Controller
{
     public function index(Request $request)
    {
        $query = Color::withCount('productColors');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $colors = $query->latest()->paginate(20)->withQueryString();

        return view('webmaster.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('webmaster.colors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'hex_code' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        Color::create($validated);

        return redirect()->route('webmaster.colors.index')
            ->with('success', 'Color created successfully.');
    }

    public function edit(Color $color)
    {
        return view('webmaster.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('colors')->ignore($color->id)],
            'hex_code' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $color->update($validated);

        return redirect()->route('webmaster.colors.index')
            ->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color)
    {
        if ($color->productColors()->exists()) {
            return back()->with('error', 'Cannot delete color assigned to products.');
        }

        $color->delete();

        return redirect()->route('webmaster.colors.index')
            ->with('success', 'Color deleted successfully.');
    }

    public function toggleStatus(Color $color)
    {
        $color->update(['is_active' => ! $color->is_active]);

        return back()->with('success', 'Color status updated.');
    }
}
