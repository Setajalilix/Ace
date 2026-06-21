<?php

namespace App\Domains\Reviews\Http\Requests;

use App\Shared\Http\Requests\LifeOSRequest;

class SaveMonthlyReviewRequest extends LifeOSRequest
{
    public function rules(): array
    {
        return ['month' => ['required', 'string'], 'content' => ['required', 'array']];
    }
}
