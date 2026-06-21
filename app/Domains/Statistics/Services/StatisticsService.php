<?php

namespace App\Domains\Statistics\Services;

use App\Domains\DailySuccess\Services\DailySuccessService;
use App\Domains\Habits\Services\HabitStatsService;
use App\Domains\DailySuccess\Enums\DayResult;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\DailySuccess\Models\DailyScore;
use App\Domains\Habits\Models\Habit;
use App\Domains\LifeAreas\Models\LifeArea;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;

class StatisticsService
{
    public function __construct(
        private DailySuccessService $dailySuccess,
        private HabitStatsService $habitStats,
    ) {}

    public function dashboard(User $user): array
    {
        $today = today();
        $month = $today->month;
        $year = $today->year;

        return [
            'daily_success_rate' => $this->dailySuccess->monthlySuccessRate($user, $year, $month),
            'tasks_completed_today' => Task::forUser($user->id)
                ->where('status', TaskStatus::Completed)
                ->whereDate('completed_at', $today)
                ->count(),
            'tasks_pending' => Task::forUser($user->id)
                ->whereIn('status', [TaskStatus::Pending, TaskStatus::InProgress])
                ->count(),
            'active_goals' => $user->goals()->count(),
            'life_area_balance' => $this->lifeAreaBalance($user),
            'weekly_completion' => $this->weeklyCompletion($user),
            'habit_consistency' => $this->habitConsistency($user),
            'time_by_category' => $this->timeByCategory($user, $today->copy()->startOfMonth(), $today),
            'monthly_scores' => $this->monthlyScores($user, $year, $month),
        ];
    }

    public function lifeAreaBalance(User $user): array
    {
        $areas = LifeArea::where('user_id', $user->id)->get();
        $counts = [];

        foreach ($areas as $area) {
            $counts[$area->id] = Task::forUser($user->id)
                ->where('life_area_id', $area->id)
                ->where('status', TaskStatus::Completed)
                ->where('completed_at', '>=', now()->subDays(30))
                ->count();
        }

        $total = array_sum($counts) ?: 1;

        return $areas->map(fn ($area) => [
            'name' => $area->name,
            'pct' => round(($counts[$area->id] ?? 0) / $total * 100, 1),
            'color' => $area->color ?? '#C47D5A',
        ])->filter(fn ($row) => $row['pct'] > 0)->values()->all();
    }

    public function weeklyCompletion(User $user): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $total = Task::forUser($user->id)->dueOn($date)->count();
            $done = Task::forUser($user->id)->dueOn($date)->where('status', TaskStatus::Completed)->count();
            $data[$date->format('D')] = $total > 0 ? round(($done / $total) * 100) : 0;
        }

        return $data;
    }

    public function habitConsistency(User $user): array
    {
        return Habit::where('user_id', $user->id)
            ->get()
            ->mapWithKeys(fn ($h) => [$h->title => $this->habitStats->monthlyConsistency($h, today()->year, today()->month)])
            ->toArray();
    }

    public function timeByCategory(User $user, Carbon $from, Carbon $to): array
    {
        return TimeBlock::where('user_id', $user->id)
            ->whereBetween('date', [$from, $to])
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    public function monthlyScores(User $user, int $year, int $month): array
    {
        return DailyScore::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->date->day => $s->result])
            ->toArray();
    }
}
