<?php

namespace App\Domains\Habits\Services;

use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitLog;
use Carbon\Carbon;

class HabitStatsService
{
    public function currentStreak(Habit $habit): int
    {
        $days = 0;
        $date = today();

        while (true) {
            if (! $this->shouldAppearOnDate($habit, $date)) {
                $date = $date->copy()->subDay();

                continue;
            }

            $log = $habit->logs()->whereDate('date', $date)->first();

            if (! $log || ! $log->completed) {
                break;
            }

            $days++;
            $date = $date->copy()->subDay();
        }

        return $days;
    }

    public function longestStreak(Habit $habit): int
    {
        $logs = $habit->logs()->where('completed', true)->orderBy('date')->get();
        if ($logs->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < $logs->count(); $i++) {
            $prev = Carbon::parse($logs[$i - 1]->date);
            $curr = Carbon::parse($logs[$i]->date);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }

        return $longest;
    }

    public function monthlyConsistency(Habit $habit, int $year, int $month): float
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $expected = 0;
        $completed = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($this->shouldAppearOnDate($habit, $date)) {
                $expected++;
                $log = $habit->logs()->whereDate('date', $date)->first();
                if ($log?->completed) {
                    $completed++;
                }
            }
        }

        return $expected > 0 ? round(($completed / $expected) * 100, 1) : 0;
    }

    public function activityGrid(Habit $habit, int $weeks = 12): array
    {
        $logs = $habit->logs()
            ->where('date', '>=', today()->subWeeks($weeks))
            ->get()
            ->keyBy(fn ($log) => $log->date->toDateString());

        $grid = [];
        $start = today()->subWeeks($weeks)->startOfWeek();

        for ($date = $start->copy(); $date->lte(today()); $date->addDay()) {
            $key = $date->toDateString();
            $log = $logs->get($key);
            $level = 0;

            if ($log?->completed) {
                if ($habit->type === 'timer' && $habit->target_minutes) {
                    $ratio = min(1, ($log->spent_minutes ?? 0) / max(1, $habit->target_minutes));
                    $level = max(1, (int) ceil($ratio * 4));
                } elseif ($habit->type === 'counter' && $habit->target_count) {
                    $ratio = min(1, ($log->count ?? 0) / max(1, $habit->target_count));
                    $level = max(1, (int) ceil($ratio * 4));
                } else {
                    $level = 4;
                }
            }

            $grid[] = ['date' => $key, 'level' => $level];
        }

        return $grid;
    }

    public function shouldAppearOnDate(Habit $habit, Carbon $date): bool
    {
        if ($habit->frequency === 'weekly' && $habit->target_days) {
            return in_array($date->dayOfWeek, $habit->target_days);
        }

        $daysPassed = Carbon::parse($habit->start_date)->diffInDays($date);

        return $daysPassed % $habit->repeat_every === 0;
    }
}
