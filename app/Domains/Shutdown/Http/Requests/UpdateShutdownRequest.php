<?php

namespace App\Domains\Shutdown\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class UpdateShutdownRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'checklist' => ['required', 'array'],
        ];
    }
}
