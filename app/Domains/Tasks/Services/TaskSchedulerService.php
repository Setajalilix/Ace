<?php

namespace App\Domains\Tasks\Services;

use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\TimeBlocks\Enums\TimeBlockStatus;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;

class TaskSchedulerService
{
    public function process(User $user): void
    {
        $now = now();

        $this->ensureDailyTimeBlocks($user, $now);

        Task::forUser($user->id)
            ->rootTasks()
            ->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Cancelled])
            ->whereNotNull('scheduled_time')
            ->where('daily_time_block', false)
            ->whereDate('due_date', '<=', $now->toDateString())
            ->each(function (Task $task) use ($now) {
                $startAt = Carbon::parse($task->due_date->toDateString().' '.$task->scheduled_time);

                if ($now->gte($startAt) && $task->status === TaskStatus::Pending) {
                    $task->update([
                        'status' => TaskStatus::InProgress,
                        'kanban_column' => KanbanColumn::Doing,
                    ]);
                }

                if ($now->isAfter($startAt->copy()->endOfDay()) && ! $task->isCompleted()) {
                    $task->update([
                        'due_date' => $now->copy()->addDay()->toDateString(),
                        'status' => TaskStatus::Pending,
                        'kanban_column' => KanbanColumn::Next,
                    ]);
                }
            });
    }

    private function ensureDailyTimeBlocks(User $user, Carbon $today): void
    {
        Task::forUser($user->id)
            ->rootTasks()
            ->where('daily_time_block', true)
            ->whereNotNull('scheduled_time')
            ->whereNotNull('block_end_time')
            ->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Cancelled])
            ->each(function (Task $task) use ($user, $today) {
                $exists = TimeBlock::where('user_id', $user->id)
                    ->forDate($today)
                    ->where('title', $task->title)
                    ->where('start_time', $task->scheduled_time)
                    ->exists();

                if ($exists) {
                    return;
                }

                TimeBlock::create([
                    'user_id' => $user->id,
                    'goal_id' => $task->goal_id,
                    'date' => $today,
                    'title' => $task->title,
                    'start_time' => $task->scheduled_time,
                    'end_time' => $task->block_end_time,
                    'latest_start_time' => Carbon::parse($task->scheduled_time)->addMinutes(15)->format('H:i:s'),
                    'objective' => $task->description,
                    'category' => $task->lifeArea?->name ?? 'Task',
                    'status' => TimeBlockStatus::Scheduled,
                ]);
            });
    }
}
