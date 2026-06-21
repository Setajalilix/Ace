<?php

namespace App\Domains\Journal\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class StoreJournalRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'type' => ['required', 'in:morning,evening,custom'],
            'content' => ['required', 'array'],
        ];
    }
}
