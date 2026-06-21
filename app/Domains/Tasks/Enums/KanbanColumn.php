<?php

namespace App\Domains\Tasks\Enums;

enum KanbanColumn: string
{
    case Backlog = 'backlog';
    case Next = 'next';
    case Doing = 'doing';
    case Waiting = 'waiting';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Backlog => 'Backlog',
            self::Next => 'Next',
            self::Doing => 'Doing',
            self::Waiting => 'Waiting',
            self::Done => 'Done',
        };
    }

    public static function ordered(): array
    {
        return [self::Backlog, self::Next, self::Doing, self::Waiting, self::Done];
    }
}
