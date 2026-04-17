<?php

namespace App\Http\Requests;

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
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:20', 'unique:users,phone'],
            'access_code' => ['required', 'string', 'max:100'],
            'password'    => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
