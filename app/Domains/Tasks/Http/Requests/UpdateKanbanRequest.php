<?php

namespace App\Domains\Tasks\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class UpdateKanbanRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return [
            'kanban_column' => ['required', 'string'],
            'kanban_sort' => ['nullable', 'integer'],
        ];
    }
}
