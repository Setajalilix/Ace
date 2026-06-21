<?php

namespace App\Domains\Reviews\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Reviews\Models\WeeklyReview;
use Carbon\Carbon;

class ReviewService
{
    public function getOrCreateWeekly(User $user, Carbon $weekStart): WeeklyReview
    {
        return WeeklyReview::firstOrCreate(
            ['user_id' => $user->id, 'week_start' => $weekStart->startOfWeek()->toDateString()],
            ['content' => WeeklyReview::defaultContent()]
        );
    }

    public function weeklySummary(User $user): array
    {
        return [
            'unprocessed_inbox' => $user->inboxItems()->unprocessed()->count(),
            'active_goals' => $user->goals()->count(),
            'active_tasks' => $user->tasks()->whereIn('status', ['pending', 'in_progress'])->count(),
            'habits_count' => $user->habits()->count(),
        ];
    }
}
