<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── List all notifications ─────────────────────────────────────────────

    public function index()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return redirect()->route('auth.login');
        }

        $contacts = $user->contacts()
            ->latest()
            ->paginate(15);

        return view('web.notifications.index', compact('contacts'));
    }

    // ─── Mark a notification as read ────────────────────────────────────────

    public function markAsRead(Request $request, string $id)
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    // ─── Mark all notifications as read ─────────────────────────────────────

    public function markAllAsRead()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return redirect()->route('auth.login');
        }

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    // ─── Delete a single notification ───────────────────────────────────────

    public function destroy(string $id)
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return redirect()->route('auth.login');
        }

        $notification = $user->notifications()->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Notification removed.');
    }

    // ─── Clear all notifications ─────────────────────────────────────────────

    public function destroyAll()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return redirect()->route('auth.login');
        }

        $user->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }

    // ─── Unread count (for AJAX polling) ────────────────────────────────────

    public function unreadCount()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
