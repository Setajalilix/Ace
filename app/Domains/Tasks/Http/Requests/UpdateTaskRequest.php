<?php

namespace App\Domains\Tasks\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class UpdateTaskRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:1,2,3'],
            'status' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
            'due_date_jalali' => ['nullable', 'string'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'goal_id' => ['nullable', 'exists:goals,id'],
            'life_area_id' => ['nullable', 'exists:life_areas,id'],
        ];
    }

    public function dueDate(): ?\Carbon\Carbon
    {
        return $this->resolvedDate('due_date');
    }
}
