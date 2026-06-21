<?php

namespace App\Domains\Events\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;

class UpdateEventRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'start_date' => ['nullable'],
            'start_date_jalali' => ['nullable', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['nullable'],
            'end_date_jalali' => ['nullable', 'string'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'life_area_id' => ['nullable', 'exists:life_areas,id'],
            'recurrence' => ['nullable', 'string'],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:99'],
            'recurrence_unit' => ['nullable', 'in:day,week,month'],
            'recurrence_days' => ['nullable', 'array'],
            'recurrence_days.*' => ['integer', 'between:0,6'],
            'recurrence_end_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:scheduled,completed,missed,cancelled,delayed'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->requireResolvedDate($validator, 'start_date', 'start date');
    }

    public function startsAt(): Carbon
    {
        $date = $this->resolvedDate('start_date') ?? today();

        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $date->toDateString().' '.$this->validated('start_time'),
            config('app.timezone')
        );
    }

    public function endsAt(): ?Carbon
    {
        if (! $this->validated('end_time')) {
            return null;
        }

        $date = $this->resolvedDate('end_date') ?? $this->resolvedDate('start_date') ?? today();

        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $date->toDateString().' '.$this->validated('end_time'),
            config('app.timezone')
        );
    }
}

