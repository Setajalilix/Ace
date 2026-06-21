<?php

namespace App\Domains\Planner\ViewModels;

use App\Domains\Auth\Models\User;
use App\Domains\Events\Enums\EventStatus;
use App\Domains\Events\Models\Event;
use App\Domains\Events\Services\EventRecurrenceService;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Shared\Calendar\JalaliDateService;
use App\Domains\DailySuccess\Services\DailySuccessService;
use App\Domains\Inbox\Services\InboxService;
use App\Domains\Planner\Services\TimelineService;
use Carbon\Carbon;

class DailyPlannerViewModel
{
    public function __construct(
        private TimelineService $timeline,
        private DailySuccessService $dailySuccess,
        private InboxService $inbox,
        private JalaliDateService $jalali,
        private EventRecurrenceService $eventRecurrence,
    ) {}

    public function build(User $user, ?Carbon $date = null): array
    {
        $date = $date ?? today();

        app(\App\Domains\Tasks\Services\TaskSchedulerService::class)->process($user);

        $tasks = $user->tasks()
            ->rootTasks()
            ->with(['goal', 'lifeArea'])
            ->dueOn($date)
            ->where('status', '!=', TaskStatus::Completed)
            ->whereNotIn('status', [TaskStatus::Cancelled])
            ->orderBy('priority')
            ->get()
            ->groupBy(fn ($t) => 'p'.$t->priority->value);

        return [
            'date' => $date,
            'dateLabel' => $date->format('l, M j, Y'),
            'jalaliLabel' => $this->jalali->format($date),
            'timeline' => $this->timeline->forDate($user, $date),
            'p1Tasks' => $tasks->get('p1', collect()),
            'p2Tasks' => $tasks->get('p2', collect()),
            'p3Tasks' => $tasks->get('p3', collect()),
            'events' => $this->eventsForDate($user, $date),
            'habits' => $user->habits()
                ->with(['logs' => fn ($q) => $q->whereDate('date', $date)])
                ->get()
                ->filter(fn ($h) => $h->shouldAppearToday() && ! $h->logs->first()?->completed),
            'notes' => $user->notes()->where('type', 'quick')->where('archived', false)->latest()->limit(5)->get(),
            'morningJournal' => $user->journalEntries()->whereDate('date', $date)->where('type', 'morning')->first(),
            'eveningJournal' => $user->journalEntries()->whereDate('date', $date)->where('type', 'evening')->first(),
            'inboxCount' => $this->inbox->unprocessedCount($user),
            'dailyScore' => $this->dailySuccess->record($user, $date),
            'shutdownLog' => $user->shutdownLogs()->whereDate('date', $date)->first(),
        ];
    }

    private function eventsForDate(User $user, Carbon $date): \Illuminate\Support\Collection
    {
        $day = $date->copy()->timezone(config('app.timezone'))->startOfDay();
        $allEvents = Event::where('user_id', $user->id)->get();

        return $this->eventRecurrence
            ->forDate($allEvents, $day)
            ->filter(fn (Event $event) => ! in_array($event->status, [EventStatus::Cancelled, EventStatus::Completed], true))
            ->sortBy(fn (Event $event) => $event->starts_at->format('H:i:s'))
            ->values();
    }
}
