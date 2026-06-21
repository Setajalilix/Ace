<?php

namespace App\Domains\Goals\Enums;

enum GoalType: string
{
    case Annual = 'annual';
    case Quarterly = 'quarterly';

    public function label(): string
    {
        return match ($this) {
            self::Annual => 'Annual Goal',
            self::Quarterly => 'Quarterly Goal',
        };
    }
}
