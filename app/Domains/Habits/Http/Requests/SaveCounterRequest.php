<?php

namespace App\Domains\Habits\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class SaveCounterRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'count' => ['required', 'integer', 'min:0'],
        ];
    }
}
