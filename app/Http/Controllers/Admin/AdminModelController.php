<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\PhoneModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminModelController extends Controller
{
    public function index(Request $request)
    {
        $query = PhoneModel::with('brand')->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $models = $query->latest()->paginate(20)->withQueryString();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.models.index', compact('models', 'brands'));
    }

    public function create()
    {
        $brands = Brand::where('is_active', true)->get();

        return view('admin.models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        PhoneModel::create($validated);

        return redirect()->route('admin.models.index')
            ->with('success', 'Phone model created successfully.');
    }

    public function edit(PhoneModel $phoneModel)
    {
        $brands = Brand::where('is_active', true)->get();

        return view('admin.models.edit', [
            'model' => $phoneModel,
            'brands' => $brands,
        ]);
    }

    public function update(Request $request, PhoneModel $phoneModel)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $phoneModel->update($validated);

        return redirect()
            ->route('admin.models.index')
            ->with('success', 'Phone model updated successfully.');
    }

    public function destroy(PhoneModel $phoneModel)
    {
        if ($phoneModel->products()->exists()) {
            return back()->with('error', 'Cannot delete model with existing products.');
        }

        $phoneModel->delete();

        return redirect()->route('admin.models.index')
            ->with('success', 'Phone model deleted successfully.');
    }

    public function toggleStatus(PhoneModel $phoneModel)
    {
        $phoneModel->update(['is_active' => ! $phoneModel->is_active]);

        return back()->with('success', 'Phone model status updated.');
    }
}
