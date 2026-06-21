<?php

namespace App\Domains\Auth\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
