<?php

namespace App\Domains\Events\Enums;

enum EventRecurrence: string
{
    case None = 'none';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Weekdays = 'weekdays';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Does not repeat',
            self::Daily => 'Every day',
            self::Weekly => 'Every week',
            self::Weekdays => 'Weekdays (Sat–Wed)',
            self::Monthly => 'Every month',
        };
    }
}
