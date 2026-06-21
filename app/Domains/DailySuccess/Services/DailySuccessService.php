<?php

namespace App\Domains\DailySuccess\Services;

use App\Domains\DailySuccess\Enums\DayResult;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\DailySuccess\Models\DailyScore;
use App\Domains\Tasks\Models\Task;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;

class DailySuccessService
{
    public function calculate(User $user, Carbon $date): DayResult
    {
        $tasks = Task::forUser($user->id)
            ->rootTasks()
            ->dueOn($date)
            ->whereNotIn('status', [TaskStatus::Cancelled])
            ->get();

        $p1 = $tasks->where('priority', TaskPriority::P1);
        $p2 = $tasks->where('priority', TaskPriority::P2);

        $p1Incomplete = $p1->filter(fn ($t) => ! $t->isCompleted());

        if ($p1Incomplete->isNotEmpty()) {
            return DayResult::Failed;
        }

        $p2Total = $p2->count();
        if ($p2Total === 0) {
            return DayResult::Success;
        }

        $p2Completed = $p2->filter(fn ($t) => $t->isCompleted())->count();
        $rate = $p2Completed / $p2Total;

        if ($rate >= 0.8) {
            return DayResult::Success;
        }

        if ($rate >= 0.5) {
            return DayResult::Average;
        }

        return DayResult::Failed;
    }

    public function record(User $user, Carbon $date): DailyScore
    {
        $tasks = Task::forUser($user->id)
            ->rootTasks()
            ->dueOn($date)
            ->whereNotIn('status', [TaskStatus::Cancelled])
            ->get();

        $p1 = $tasks->where('priority', TaskPriority::P1);
        $p2 = $tasks->where('priority', TaskPriority::P2);

        $result = $this->calculate($user, $date);

        return DailyScore::updateOrCreate(
            ['user_id' => $user->id, 'date' => $date->toDateString()],
            [
                'p1_total' => $p1->count(),
                'p1_completed' => $p1->filter(fn ($t) => $t->isCompleted())->count(),
                'p2_total' => $p2->count(),
                'p2_completed' => $p2->filter(fn ($t) => $t->isCompleted())->count(),
                'result' => $result,
            ]
        );
    }

    public function monthlySuccessRate(User $user, int $year, int $month): float
    {
        $scores = DailyScore::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        if ($scores->isEmpty()) {
            return 0;
        }

        $successful = $scores->where('result', DayResult::Success)->count();

        return round(($successful / $scores->count()) * 100, 1);
    }
}
