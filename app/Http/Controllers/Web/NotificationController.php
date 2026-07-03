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
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('web.notifications.index', compact('notifications'));
    }

    // ─── Mark a notification as read ────────────────────────────────────────

    public function markAsRead(Request $request, string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    // ─── Mark all notifications as read ─────────────────────────────────────

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    // ─── Delete a single notification ───────────────────────────────────────

    public function destroy(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Notification removed.');
    }

    // ─── Clear all notifications ─────────────────────────────────────────────

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }

    // ─── Unread count (for AJAX polling) ────────────────────────────────────

    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
