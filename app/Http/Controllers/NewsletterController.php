<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ]);

        // For now, just return success - you can add database storage later
        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
