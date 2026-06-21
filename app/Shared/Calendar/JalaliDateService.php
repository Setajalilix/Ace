<?php

namespace App\Shared\Calendar;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliDateService
{
    public function format(Carbon $date, string $format = 'Y/m/d'): string
    {
        return Jalalian::fromCarbon($date)->format($format);
    }

    public function dualFormat(Carbon $date): string
    {
        $gregorian = $date->format('l, M j, Y');
        $jalali = $this->format($date, 'l j F Y');

        return "{$gregorian} · {$jalali}";
    }

    public function monthName(Carbon $date): string
    {
        return Jalalian::fromCarbon($date)->format('F Y');
    }

    public function months(): array
    {
        return [
            1 => 'Farvardin',
            2 => 'Ordibehesht',
            3 => 'Khordad',
            4 => 'Tir',
            5 => 'Mordad',
            6 => 'Shahrivar',
            7 => 'Mehr',
            8 => 'Aban',
            9 => 'Azar',
            10 => 'Dey',
            11 => 'Bahman',
            12 => 'Esfand',
        ];
    }

    public function years(int $before = 5, int $after = 5): array
    {
        $current = Jalalian::now()->getYear();

        return range($current - $before, $current + $after);
    }

    public function daysInMonth(int $year, int $month): int
    {
        return Jalalian::fromFormat('Y/n/j', "{$year}/{$month}/1")->getMonthDays();
    }

    public function parse(string $jalali): Carbon
    {
        $parts = array_map('intval', explode('/', str_replace('-', '/', $jalali)));

        return Jalalian::fromFormat('Y/n/j', "{$parts[0]}/{$parts[1]}/{$parts[2]}")->toCarbon()->startOfDay();
    }
}
