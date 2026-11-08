<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiRegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $firstName = trim((string) $request->input('first_name', ''));
        $lastName = trim((string) $request->input('last_name', ''));
        $fullName = trim((string) $request->input('name', ''));

        if ($firstName === '' && $fullName !== '') {
            $parts = explode(' ', $fullName, 2);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
        }

        if ($fullName === '') {
            $fullName = trim($firstName . ' ' . $lastName);
        }

        if ($fullName === '') {
            return response()->json([
                'success' => false,
                'message' => 'Name or first_name is required',
                'errors' => ['name' => ['The name field is required.']],
            ], 422);
        }

        $roleId = null;
        try {
            if (class_exists(Role::class)) {
                $roleId = Role::where('slug', 'customer')->orWhere('is_default', true)->value('id');
            }
        } catch (\Throwable $e) {
            $roleId = null;
        }

        $user = User::create([
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'password' => Hash::make($request->input('password')),
            'role' => User::ROLE_CUSTOMER,
            'role_id' => $roleId,
            'status' => 'active',
            'is_active' => true,
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;
        $userData = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $userData,
            'data' => [
                'token' => $token,
                'user' => $userData,
            ],
        ], 201);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country ?? null,
            'postal_code' => $user->postal_code ?? null,
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role ?? User::ROLE_CUSTOMER,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
