<?php

namespace App\Domains\TimeBlocks\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class RescheduleTimeBlockRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date'],
            'date_jalali' => ['nullable', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function blockDate(): \Carbon\Carbon
    {
        return $this->resolvedDate('date') ?? today();
    }
}
