<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminStorageController extends Controller
{
    public function index(Request $request)
    {
        $query = PhoneStorage::withCount('productStorages');

        if ($request->filled('search')) {
            $query->where('capacity', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $storages = $query->latest()->paginate(20)->withQueryString();

        return view('admin.storage.index', compact('storages'));
    }

    public function create()
    {
        return view('admin.storage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'capacity' => 'required|string|max:50|unique:phone_storages,capacity',
            'is_active' => 'boolean',
        ]);

        PhoneStorage::create($validated);

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage option created successfully.');
    }

    public function edit(PhoneStorage $storage)
    {
        return view('admin.storage.edit', compact('storage'));
    }

    public function update(Request $request, PhoneStorage $storage)
    {
        $validated = $request->validate([
            'capacity' => ['required', 'string', 'max:50', Rule::unique('phone_storages')->ignore($storage->id)],
            'is_active' => 'boolean',
        ]);

        $storage->update($validated);

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage option updated successfully.');
    }

    public function destroy(PhoneStorage $storage)
    {
        if ($storage->productStorages()->exists()) {
            return back()->with('error', 'Cannot delete storage option assigned to products.');
        }

        $storage->delete();

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage option deleted successfully.');
    }

    public function toggleStatus(PhoneStorage $storage)
    {
        $storage->update(['is_active' => ! $storage->is_active]);

        return back()->with('success', 'Storage status updated.');
    }
}
