<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactApiController extends Controller
{
    /**
     * GET /api/v1/contact
     * List the authenticated user's support enquiries and replies.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $contacts = Contact::where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if (! empty($user->email)) {
                $q->orWhere('email', $user->email);
            }
        })
            ->latest()
            ->get()
            ->map(fn (Contact $c) => $this->transformContact($c));

        return response()->json([
            'success' => true,
            'contacts' => $contacts,
            'data' => $contacts,
        ]);
    }

    /**
     * GET /api/v1/contact/{contact}
     * Show a single support enquiry and any reply from the support team.
     */
    public function show(Request $request, Contact $contact): JsonResponse
    {
        $user = $request->user();
        if ($contact->user_id !== $user->id && $contact->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this message.',
            ], 403);
        }

        $data = $this->transformContact($contact);

        return response()->json([
            'success' => true,
            'contact' => $data,
            'data' => $data,
        ]);
    }

    /**
     * POST /api/v1/contact
     * Submit a contact message.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        // Pre-fill from authenticated user if available
        if ($request->user()) {
            $validated['name'] = $validated['name'] ?: $request->user()->name;
            $validated['email'] = $validated['email'] ?: $request->user()->email;
            $validated['phone'] = $validated['phone'] ?? $request->user()->phone;
        }

        $validated['user_id'] = $request->user()?->id;
        $validated['status'] = 'pending';

        $contact = Contact::create($validated);
        $data = $this->transformContact($contact);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent. We will get back to you soon!',
            'contact' => $data,
            'data' => $data,
        ], 201);
    }

    private function transformContact(Contact $contact): array
    {
        return [
            'id' => $contact->id,
            'user_id' => $contact->user_id,
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'subject' => $contact->subject ?? 'General Enquiry',
            'message' => $contact->message,
            'status' => $contact->status ?? ($contact->reply ? 'replied' : 'pending'),
            'reply' => $contact->reply,
            'replied_at' => $contact->replied_at ? (string) $contact->replied_at : null,
            'created_at' => $contact->created_at?->toIso8601String(),
            'created_at_human' => $contact->created_at?->diffForHumans(),
        ];
    }
}