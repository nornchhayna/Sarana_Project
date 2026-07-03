<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('subject', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->latest()->paginate(20)->withQueryString();

        $counts = [
            'unread' => Contact::where('status', 'unread')->count(),
            'read' => Contact::where('status', 'read')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'counts'));
    }

    public function show(Contact $contact)
    {
        // Auto-mark as read
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:10',
        ]);

        $contact->update([
            'reply' => $validated['reply'],
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        // Send reply email
        try {
            Mail::raw($validated['reply'], function ($message) use ($contact) {
                $message->to($contact->email, $contact->name)
                    ->subject('Re: '.($contact->subject ?? 'Your enquiry'));
            });
        } catch (\Exception $e) {
            return back()->with('warning', 'Reply saved but email could not be sent: '.$e->getMessage());
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function markRead(Contact $contact)
    {
        $contact->update(['status' => 'read']);

        return back()->with('success', 'Marked as read.');
    }

    public function bulkMarkRead(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:contacts,id']);

        Contact::whereIn('id', $request->ids)->where('status', 'unread')->update(['status' => 'read']);

        return back()->with('success', 'Selected contacts marked as read.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:contacts,id']);

        Contact::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids).' contacts deleted.');
    }
}
