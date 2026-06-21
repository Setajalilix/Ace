<?php

namespace App\Domains\TimeBlocks\Enums;

enum TimeBlockStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Missed = 'missed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Missed => 'Missed',
            self::Skipped => 'Skipped',
        };
    }
}
