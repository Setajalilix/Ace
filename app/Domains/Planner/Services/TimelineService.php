<?php

namespace App\Domains\Planner\Services;

use App\Domains\Events\Models\Event;
use App\Domains\Events\Services\EventRecurrenceService;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimelineService
{
    public function __construct(private EventRecurrenceService $eventRecurrence) {}

    public function forDate(User $user, Carbon $date): Collection
    {
        $items = collect();

        $blocks = TimeBlock::where('user_id', $user->id)
            ->forDate($date)
            ->orderBy('start_time')
            ->get();

        foreach ($blocks as $block) {
            $items->push([
                'type' => 'time_block',
                'time' => $block->start_time,
                'end_time' => $block->end_time,
                'title' => $block->title,
                'subtitle' => $block->category,
                'objective' => $block->objective,
                'model' => $block,
                'sort' => Carbon::parse($block->start_time)->format('Hi'),
            ]);
        }

        $day = $date->copy()->timezone(config('app.timezone'))->startOfDay();
        $events = $this->eventRecurrence
            ->forDate(Event::where('user_id', $user->id)->get(), $day);

        foreach ($events as $event) {
            $items->push([
                'type' => 'event',
                'time' => $event->starts_at->format('H:i:s'),
                'end_time' => $event->ends_at?->format('H:i:s'),
                'title' => $event->title,
                'subtitle' => $event->location,
                'objective' => null,
                'model' => $event,
                'sort' => $event->starts_at->format('Hi'),
            ]);
        }

        $tasks = Task::forUser($user->id)
            ->rootTasks()
            ->dueOn($date)
            ->whereNotNull('scheduled_time')
            ->orderBy('scheduled_time')
            ->get();

        foreach ($tasks as $task) {
            $items->push([
                'type' => 'task',
                'time' => $task->scheduled_time,
                'end_time' => null,
                'title' => $task->title,
                'subtitle' => 'P'.$task->priority->value,
                'objective' => null,
                'model' => $task,
                'sort' => Carbon::parse($task->scheduled_time)->format('Hi'),
            ]);
        }

        return $items->sortBy('sort')->values();
    }
}
