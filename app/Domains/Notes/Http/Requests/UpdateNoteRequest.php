<?php

namespace App\Domains\Notes\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class UpdateNoteRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'pinned' => ['nullable', 'boolean'],
        ];
    }
}
