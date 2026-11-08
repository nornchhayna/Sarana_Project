<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $userData = $this->formatUserWithCounts($user);

        return response()->json([
            'success' => true,
            'user' => $userData,
            'data' => $userData,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:120',
            'province' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:30',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (! empty($data['first_name']) || ! empty($data['last_name'])) {
            $firstName = $data['first_name'] ?? $user->first_name ?? '';
            $lastName = $data['last_name'] ?? $user->last_name ?? '';
            $data['name'] = trim($firstName . ' ' . $lastName);
        } elseif (! empty($data['name'])) {
            $parts = explode(' ', trim($data['name']), 2);
            $data['first_name'] = $parts[0] ?? '';
            $data['last_name'] = $parts[1] ?? '';
        }

        $user->update($data);
        $user->refresh();
        $userData = $this->formatUserWithCounts($user);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $userData,
            'data' => $userData,
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $rules = [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6',
        ];
        if ($request->has('password_confirmation')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'errors' => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Invalid image file',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($user->avatar && ! str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);
        $user->refresh();

        $userData = $this->formatUserWithCounts($user);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'avatar_url' => $user->avatar_url,
            'user' => $userData,
            'data' => $userData,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }

    private function formatUserWithCounts(User $user): array
    {
        $ordersCount = 0;
        $wishlistCount = 0;
        $reviewsCount = 0;

        try {
            $ordersCount = $user->orders()->count();
            $wishlistCount = $user->wishlists()->count();
            $reviewsCount = $user->reviews()->count();
        } catch (\Throwable $e) {
            // Safe fallback
        }

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'province' => $user->province,
            'country' => $user->country ?? null,
            'postal_code' => $user->postal_code ?? null,
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth?->format('Y-m-d'),
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role ?? User::ROLE_CUSTOMER,
            'orders_count' => $ordersCount,
            'wishlist_count' => $wishlistCount,
            'reviews_count' => $reviewsCount,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
