<?php

namespace App\Domains\Events\Enums;

enum EventStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Missed = 'missed';
    case Cancelled = 'cancelled';
    case Delayed = 'delayed';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Completed => 'Completed',
            self::Missed => 'Missed',
            self::Cancelled => 'Cancelled',
            self::Delayed => 'Delayed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => '#6B9BD1',
            self::Completed => '#7BAE7F',
            self::Missed => '#E05D44',
            self::Cancelled => '#C4B5A5',
            self::Delayed => '#E6A23C',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Scheduled => 'calendar',
            self::Completed => 'check-circle',
            self::Missed => 'x',
            self::Cancelled => 'x',
            self::Delayed => 'clock',
        };
    }

    public function bgClass(): string
    {
        return match ($this) {
            self::Scheduled => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Completed => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Missed => 'bg-red-50 text-red-700 border-red-200',
            self::Cancelled => 'bg-stone-50 text-stone-500 border-stone-200',
            self::Delayed => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }
}
