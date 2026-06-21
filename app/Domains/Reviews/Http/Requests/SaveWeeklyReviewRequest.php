<?php

namespace App\Domains\Reviews\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class SaveWeeklyReviewRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return ['week_start' => ['required', 'date'], 'content' => ['required', 'array']];
    }
}
