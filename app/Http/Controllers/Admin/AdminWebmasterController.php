<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminWebmasterController extends Controller
{
    /**
     * Display a listing of webmasters.
     */
    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_WEBMASTER);

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $webmasters = $query->latest()->paginate(15)->withQueryString();

        // Statistics cards
        $stats = [
            'total' => User::where('role', User::ROLE_WEBMASTER)->count(),
            'active' => User::where('role', User::ROLE_WEBMASTER)->where('is_active', true)->count(),
            'inactive' => User::where('role', User::ROLE_WEBMASTER)->where('is_active', false)->count(),
            'recent' => User::where('role', User::ROLE_WEBMASTER)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.webmasters.index', compact('webmasters', 'stats'));
    }

    /**
     * Show the form for creating a new webmaster.
     */
    public function create()
    {
        return view('admin.webmasters.create');
    }

    /**
     * Store a newly created webmaster in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'gender' => ['nullable', 'in:male,female,other'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $webmaster = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_WEBMASTER,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
            'gender' => $validated['gender'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('admin.webmasters.index')
            ->with('success', "Webmaster \"{$webmaster->name}\" created successfully.");
    }

    /**
     * Display the specified webmaster.
     */
    public function show(User $webmaster)
    {
        if ($webmaster->role !== User::ROLE_WEBMASTER) {
            return redirect()->route('admin.webmasters.index')
                ->with('error', 'The requested user is not a webmaster.');
        }

        return view('admin.webmasters.show', compact('webmaster'));
    }

    /**
     * Show the form for editing the specified webmaster.
     */
    public function edit(User $webmaster)
    {
        if ($webmaster->role !== User::ROLE_WEBMASTER) {
            return redirect()->route('admin.webmasters.index')
                ->with('error', 'The requested user is not a webmaster.');
        }

        return view('admin.webmasters.edit', compact('webmaster'));
    }

    /**
     * Update the specified webmaster in storage.
     */
    public function update(Request $request, User $webmaster)
    {
        if ($webmaster->role !== User::ROLE_WEBMASTER) {
            return redirect()->route('admin.webmasters.index')
                ->with('error', 'The requested user is not a webmaster.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($webmaster->id)],
            'phone' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($webmaster->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'gender' => ['nullable', 'in:male,female,other'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'gender' => $validated['gender'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $webmaster->update($updateData);

        return redirect()->route('admin.webmasters.index')
            ->with('success', "Webmaster \"{$webmaster->name}\" updated successfully.");
    }

    /**
     * Toggle active/suspended status of webmaster.
     */
    public function toggleStatus(User $webmaster)
    {
        if ($webmaster->role !== User::ROLE_WEBMASTER) {
            return back()->with('error', 'The specified user is not a webmaster.');
        }

        if ($webmaster->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $webmaster->update([
            'is_active' => ! $webmaster->is_active,
        ]);

        $status = $webmaster->is_active ? 'activated' : 'suspended';

        return back()->with('success', "Webmaster \"{$webmaster->name}\" {$status} successfully.");
    }

    /**
     * Remove the specified webmaster from storage.
     */
    public function destroy(User $webmaster)
    {
        if ($webmaster->role !== User::ROLE_WEBMASTER) {
            return redirect()->route('admin.webmasters.index')
                ->with('error', 'The specified user is not a webmaster.');
        }

        if ($webmaster->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $webmaster->name;
        $webmaster->delete();

        return redirect()->route('admin.webmasters.index')
            ->with('success', "Webmaster \"{$name}\" deleted successfully.");
    }
}
