<?php

namespace App\Domains\Auth\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class LoginRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}
