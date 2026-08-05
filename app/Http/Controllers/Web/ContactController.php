<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // ─── Show contact form ──────────────────────────────────────────────────

    public function index()
    {
        return view('web.contact.index');
    }

    // ─── Submit contact message ─────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        // Pre-fill from authenticated user if available
        if (auth()->check()) {
            $validated['name'] = $validated['name'] ?? auth()->user()->name;
            $validated['email'] = $validated['email'] ?? auth()->user()->email;
        }

        $validated['user_id'] = auth()->id();
        Contact::create($validated);

        return back()->with('success', 'Your message has been sent. We will get back to you soon!');
    }
}
