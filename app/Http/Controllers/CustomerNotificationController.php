<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    // AJAX notification dropdown
    public function index()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        if (! $user) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        $notifications = $user->unseenReplies()->get();

        return response()->json([
            'count' => $notifications->count(),
            'items' => $notifications->map(fn ($c) => [
                'id' => $c->id,
                'subject' => $c->subject ?? 'Your enquiry',
                'reply' => \Str::limit($c->reply, 80),
                'replied_at' => $c->replied_at?->diffForHumans(),
                'url' => route('web.contact.show', $c),
            ]),
        ]);
    }

    // Full notification page
    public function all()
    {
        // Get user from multiple guards
        $user = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
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

    public function show(Contact $contact)
    {
        // Get user ID from multiple guards
        $userId = null;
        foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $userId = Auth::guard($guard)->id();
                break;
            }
        }

        abort_unless($contact->user_id === $userId, 403);

        $contact->update([
            'is_seen_by_customer' => true,
        ]);

        return view('web.contact.show', compact('contact'));
    }
}
