<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('sort_order')->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|image|max:4096',
            'button_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['image'] = $request->file('image')->store('sliders', 'public');

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider created successfully.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'button_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image);
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image);
        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:sliders,id',
        ]);

        foreach ($validated['order'] as $sortOrder => $id) {
            Slider::where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        return back()->with('success', 'Slider status updated.');
    }
}
