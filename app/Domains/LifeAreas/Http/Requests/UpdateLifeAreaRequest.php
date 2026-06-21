<?php

namespace App\Domains\LifeAreas\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class UpdateLifeAreaRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
