<?php

namespace App\Domains\Habits\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class SaveTimerRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'spent_minutes' => ['required', 'integer', 'min:0'],
        ];
    }
}
