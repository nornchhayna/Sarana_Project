<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.security', [
            'settings' => Setting::group('security'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'two_factor_required' => ['sometimes', 'boolean'],
            'session_lifetime' => ['required', 'integer', 'min:5', 'max:1440'],
            'password_min_length' => ['nullable', 'integer', 'min:6', 'max:64'],
        ]);

        Setting::set('two_factor_required', $validated['two_factor_required'] ?? false, 'security', 'boolean');
        Setting::set('session_lifetime', $validated['session_lifetime'], 'security', 'integer');

        if (! empty($validated['password_min_length'])) {
            Setting::set('password_min_length', $validated['password_min_length'], 'security', 'integer');
        }

        return back()->with('status', 'Security settings updated.');
    }

    /**
     * Force-logout every session for every user except the current one,
     * by clearing the sessions table (assumes the database session driver).
     */
    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        DB::table('sessions')
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('status', 'All other active sessions have been logged out.');
    }
}
