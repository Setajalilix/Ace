<?php

namespace App\Domains\Reviews\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Reviews\Http\Requests\SaveMonthlyReviewRequest;
use App\Domains\Reviews\Http\Requests\SaveWeeklyReviewRequest;
use App\Domains\Reviews\Models\MonthlyReview;
use App\Domains\Reviews\Models\WeeklyReview;
use App\Domains\DailySuccess\Services\DailySuccessService;
use App\Domains\Reviews\Services\ReviewService;
use App\Domains\Statistics\Services\StatisticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function weekly(Request $request, ReviewService $reviewService)
    {
        $weekStart = $request->week
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $review = $reviewService->getOrCreateWeekly($request->user(), $weekStart);
        $summary = $reviewService->weeklySummary($request->user());

        return view('reviews.weekly', compact('review', 'summary', 'weekStart'));
    }

    public function saveWeekly(SaveWeeklyReviewRequest $request)
    {
        WeeklyReview::updateOrCreate(
            ['user_id' => $request->user()->id, 'week_start' => $request->validated('week_start')],
            ['content' => $request->validated('content')]
        );

        return back()->with('success', 'Weekly review saved.');
    }

    public function monthly(Request $request, StatisticsService $stats, DailySuccessService $dailySuccess)
    {
        $date = $request->month ? Carbon::parse($request->month.'-01') : now();
        $user = $request->user();

        $review = MonthlyReview::firstOrCreate(
            ['user_id' => $user->id, 'month' => $date->format('Y-m')],
            ['content' => MonthlyReview::defaultContent()]
        );

        $statistics = [
            'success_rate' => $dailySuccess->monthlySuccessRate($user, $date->year, $date->month),
            'life_area_balance' => $stats->lifeAreaBalance($user),
            'habit_consistency' => $stats->habitConsistency($user),
            'completed_goals' => $user->goals()->where('progress', 100)->count(),
        ];

        return view('reviews.monthly', compact('review', 'statistics', 'date'));
    }

    public function saveMonthly(SaveMonthlyReviewRequest $request)
    {
        MonthlyReview::updateOrCreate(
            ['user_id' => $request->user()->id, 'month' => $request->validated('month')],
            ['content' => $request->validated('content')]
        );

        return back()->with('success', 'Monthly review saved.');
    }
}
