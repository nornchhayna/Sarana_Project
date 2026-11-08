<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneRam;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRamController extends Controller
{
    public function index(Request $request)
    {
        $query = PhoneRam::withCount('productRams');

        if ($request->filled('search')) {
            $query->where('capacity', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $rams = $query->latest()->paginate(20)->withQueryString();

        return view('admin.rams.index', compact('rams'));
    }

    public function create()
    {
        return view('admin.rams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'capacity' => 'required|string|max:50|unique:phone_rams,capacity',
            'label' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        PhoneRam::create($validated);

        return redirect()->route('admin.rams.index')
            ->with('success', 'RAM option created successfully.');
    }

    public function edit($id)
    {
        $ram = PhoneRam::findOrFail($id);

        return view('admin.rams.edit', compact('ram'));
    }

    public function update(Request $request, PhoneRam $ram)
    {
        $validated = $request->validate([
            'capacity' => ['required', 'string', 'max:50', Rule::unique('phone_rams')->ignore($ram->id)],
            'label' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $ram->update($validated);

        return redirect()->route('admin.rams.index')
            ->with('success', 'RAM option updated successfully.');
    }

    public function destroy(PhoneRam $ram)
    {
        if ($ram->productRams()->exists()) {
            return back()->with('error', 'Cannot delete RAM option assigned to products.');
        }

        $ram->delete();

        return redirect()->route('admin.rams.index')
            ->with('success', 'RAM option deleted successfully.');
    }

    public function toggleStatus(PhoneRam $ram)
    {
        $ram->update(['is_active' => ! $ram->is_active]);

        return back()->with('success', 'RAM status updated.');
    }
}
