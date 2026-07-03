<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],   // email OR phone
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Please enter your email or phone number.',
            'password.required' => 'Please enter your password.',
        ];
    }

    /**
     * Detect if the login field looks like a phone number.
     */
    public function isPhone(): bool
    {
        $login = $this->input('login');

        return (bool) preg_match('/^[0\+8][0-9]{7,14}$/', preg_replace('/\s/', '', $login));
    }

    /**
     * Return the field name for auth attempt.
     */
    public function loginField(): string
    {
        return $this->isPhone() ? 'phone' : 'email';
    }
}
