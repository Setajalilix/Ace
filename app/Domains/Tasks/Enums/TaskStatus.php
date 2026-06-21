<?php

namespace App\Domains\Tasks\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Delayed = 'delayed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Delayed => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => '#A8958B',
            self::InProgress => '#6B9BD1',
            self::Completed => '#7BAE7F',
            self::Cancelled => '#C4B5A5',
            self::Delayed => '#E6A23C',
        };
    }

    public function bgClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-stone-100 text-stone-600 border-stone-200',
            self::InProgress => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Completed => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Cancelled => 'bg-stone-50 text-stone-400 border-stone-100',
            self::Delayed => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'circle',
            self::InProgress => 'play',
            self::Completed => 'check',
            self::Cancelled => 'x',
            self::Delayed => 'clock',
        };
    }
}
