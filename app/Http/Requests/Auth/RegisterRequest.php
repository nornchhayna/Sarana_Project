<?php

namespace App\Http\Requests\Auth;

use App\Services\OtpService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',   // DNS check built-in
                'max:255',
                'unique:users,email',
                function ($attr, $value, $fail) {
                    $otpService = app(OtpService::class);
                    if (! $otpService->isRealEmailDomain($value)) {
                        $fail('The email domain does not appear to be valid or does not exist.');
                    }
                },
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone',
                function ($attr, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $otpService = app(OtpService::class);
                    if (! $otpService->isValidCambodianPhone($value)) {
                        $fail('Please enter a valid Cambodian phone number (e.g. 0xx xxx xxxx).');
                    }
                },
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'Passwords do not match.',
        ];
    }
}
