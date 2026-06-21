<?php

namespace App\Shared\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class LifeOSRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
