<?php

namespace App\Domains\Tasks\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class ScheduleTaskTimeBlockRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'block_date' => ['nullable'],
            'block_date_jalali' => ['nullable', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'repeat_daily' => ['nullable', 'boolean'],
        ];
    }

    public function blockDate(): \Carbon\Carbon
    {
        return $this->resolvedDate('block_date') ?? today();
    }
}
