<?php

namespace App\Domains\Inbox\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class QuickCaptureRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
