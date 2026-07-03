<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * /dashboard — smart redirect based on role.
     *
     * This is the only method needed here now. Each role has its own
     * dedicated controller (AdminDashboardController, etc.) and its own
     * route file, so this controller just decides where to send the user.
     */
    public function redirect(): RedirectResponse
    {
        $user = auth()->user();

        return match ($user?->role) {
            'admin', 'webmaster' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
