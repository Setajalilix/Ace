<?php

namespace App\Domains\Goals\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class UpdateGoalRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'why' => ['nullable', 'string'],
            'success_criteria' => ['nullable', 'string'],
            'type' => ['required', 'in:annual,quarterly'],
            'target_date' => ['nullable', 'date'],
            'target_date_jalali' => ['nullable', 'string'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'life_area_id' => ['nullable', 'exists:life_areas,id'],
        ];
    }

    public function targetDate(): ?\Carbon\Carbon
    {
        return $this->resolvedDate('target_date');
    }
}
