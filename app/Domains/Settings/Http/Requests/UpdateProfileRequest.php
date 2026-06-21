<?php

namespace App\Domains\Settings\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
        ];
    }
}
