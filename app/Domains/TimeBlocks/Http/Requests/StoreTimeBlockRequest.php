<?php

namespace App\Domains\TimeBlocks\Http\Requests;

use App\Shared\Http\Requests\Concerns\ParsesDates;
use App\Shared\Http\Requests\LifeOSRequest;

class StoreTimeBlockRequest extends LifeOSRequest
{
    use ParsesDates;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'latest_start_time' => ['nullable', 'date_format:H:i'],
            'category' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
        ];
    }

    public function blockDate(): \Carbon\Carbon
    {
        return $this->resolvedDate('date') ?? today();
    }
}
