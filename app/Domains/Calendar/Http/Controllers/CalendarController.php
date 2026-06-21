<?php

namespace App\Domains\Calendar\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\DailySuccess\Models\DailyScore;
use App\Domains\Events\Models\Event;
use App\Domains\Journal\Models\JournalEntry;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Shared\Calendar\JalaliDateService;
use App\Domains\Events\Services\EventRecurrenceService;
use App\Domains\TimeBlocks\Services\TimeBlockService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(
        Request $request,
        JalaliDateService $jalali,
        TimeBlockService $timeBlockService,
        EventRecurrenceService $recurrence,
    ) {
        $view = $request->get('view', 'month');
        $date = $request->date ? Carbon::parse($request->date) : today();
        $user = $request->user();

        $timeBlockService->checkMissedBlocks($user, $date);

        $data = match ($view) {
            'day' => $this->dayView($user, $date, $recurrence),
            'week' => $this->weekView($user, $date, $recurrence),
            default => $this->monthView($user, $date, $recurrence),
        };

        $scores = DailyScore::where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->get()
            ->keyBy(fn ($s) => $s->date->day);

        $prevDate = match ($view) {
            'week' => $date->copy()->subWeek(),
            'day' => $date->copy()->subDay(),
            default => $date->copy()->subMonth(),
        };
        $nextDate = match ($view) {
            'week' => $date->copy()->addWeek(),
            'day' => $date->copy()->addDay(),
            default => $date->copy()->addMonth(),
        };

        return view('calendar.index', array_merge($data, [
            'view' => $view,
            'date' => $date,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'jalaliLabel' => $jalali->dualFormat($date),
            'scores' => $scores,
        ]));
    }

    public function dateParts(Request $request, JalaliDateService $jalali)
    {
        $request->validate(['date' => ['required', 'date']]);
        $date = Carbon::parse($request->date);

        return response()->json([
            'gregorian' => $date->toDateString(),
            'year' => (int) $jalali->format($date, 'Y'),
            'month' => (int) $jalali->format($date, 'n'),
            'day' => (int) $jalali->format($date, 'j'),
            'jalali' => $jalali->format($date, 'Y/n/j'),
        ]);
    }

    private function dayView($user, Carbon $date, EventRecurrenceService $recurrence): array
    {
        $allEvents = Event::where('user_id', $user->id)->get();

        return [
            'tasks' => Task::forUser($user->id)->dueOn($date)->incompleteFirst()->get(),
            'events' => $recurrence->forDate($allEvents, $date),
            'timeBlocks' => TimeBlock::where('user_id', $user->id)->forDate($date)->get(),
            'journals' => JournalEntry::where('user_id', $user->id)->whereDate('date', $date)->get(),
        ];
    }

    private function weekView($user, Carbon $date, EventRecurrenceService $recurrence): array
    {
        $start = $date->copy()->startOfWeek();
        $end = $date->copy()->endOfWeek();
        $allEvents = Event::where('user_id', $user->id)->get();

        return [
            'days' => collect(range(0, 6))->map(fn ($i) => $start->copy()->addDays($i)),
            'tasks' => Task::forUser($user->id)
                ->whereDate('due_date', '>=', $start->toDateString())
                ->whereDate('due_date', '<=', $end->toDateString())
                ->get(),
            'events' => $allEvents,
            'recurrence' => $recurrence,
            'timeBlocks' => TimeBlock::where('user_id', $user->id)->whereDate('date', '>=', $start->toDateString())->whereDate('date', '<=', $end->toDateString())->get(),
        ];
    }

    private function monthView($user, Carbon $date, EventRecurrenceService $recurrence): array
    {
        $start = $date->copy()->startOfMonth()->startOfWeek();
        $end = $date->copy()->endOfMonth()->endOfWeek();
        $allEvents = Event::where('user_id', $user->id)->get();
        $allTasks = Task::forUser($user->id)
            ->whereDate('due_date', '>=', $start->toDateString())
            ->whereDate('due_date', '<=', $end->toDateString())
            ->get();

        return [
            'weeks' => $this->buildWeeks($start, $end),
            'tasks' => $allTasks,
            'events' => $allEvents,
            'recurrence' => $recurrence,
            'timeBlocks' => TimeBlock::where('user_id', $user->id)->whereDate('date', '>=', $start->toDateString())->whereDate('date', '<=', $end->toDateString())->get(),
        ];
    }

    private function buildWeeks(Carbon $start, Carbon $end): array
    {
        $weeks = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = $current->copy()->addDays($i);
            }
            $weeks[] = $week;
            $current->addWeek();
        }

        return $weeks;
    }
}
