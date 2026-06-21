<?php

namespace App\Domains\Journal\Enums;

enum JournalType: string
{
    case Morning = 'morning';
    case Evening = 'evening';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Morning => 'Morning Journal',
            self::Evening => 'Evening Journal',
            self::Custom => 'Custom Entry',
        };
    }
}
