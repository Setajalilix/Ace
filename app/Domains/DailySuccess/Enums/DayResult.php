<?php

namespace App\Domains\DailySuccess\Enums;

enum DayResult: string
{
    case Success = 'success';
    case Average = 'average';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Successful',
            self::Average => 'Average',
            self::Failed => 'Failed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Success => 'bg-emerald-500',
            self::Average => 'bg-amber-400',
            self::Failed => 'bg-red-500',
        };
    }
}
