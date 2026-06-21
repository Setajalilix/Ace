<?php

namespace App\Domains\Habits\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateHabitRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string'],
            'type' => ['required', 'in:checkbox,timer,counter'],
            'repeat_every' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable'],
            'start_date_jalali' => ['nullable', 'string'],
            'target_minutes' => ['nullable', 'integer', 'min:0'],
            'target_count' => ['nullable', 'integer', 'min:1'],
            'daily_increment' => ['nullable', 'integer', 'min:0'],
            'life_area_id' => ['nullable', 'exists:life_areas,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->requireResolvedDate($validator, 'start_date', 'start date');
    }

    public function startDate(): \Carbon\Carbon
    {
        return $this->resolvedDate('start_date') ?? today();
    }
}
