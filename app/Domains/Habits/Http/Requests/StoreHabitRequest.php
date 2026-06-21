<?php

namespace App\Domains\Habits\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class StoreHabitRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string'],
            'type' => ['required', 'in:checkbox,timer,counter'],
            'repeat_every' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'target_minutes' => ['nullable', 'integer', 'min:0'],
            'daily_increment' => ['nullable', 'integer', 'min:0'],
            'target_count' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function startDate(): \Carbon\Carbon
    {
        return $this->resolvedDate('start_date') ?? today();
    }
}
