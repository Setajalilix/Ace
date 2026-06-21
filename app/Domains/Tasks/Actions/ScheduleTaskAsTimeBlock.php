<?php

namespace App\Domains\Tasks\Actions;

use App\Domains\TimeBlocks\Enums\TimeBlockStatus;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use Carbon\Carbon;

class ScheduleTaskAsTimeBlock
{
    public function execute(
        Task $task,
        Carbon $date,
        string $startTime,
        string $endTime,
        bool $repeatDaily = false,
    ): TimeBlock {
        $start = Carbon::parse($date->toDateString().' '.$startTime);
        $end = Carbon::parse($date->toDateString().' '.$endTime);

        if ($end->lte($start)) {
            $end->addDay();
        }

        $minutes = $start->diffInMinutes($end);

        $block = TimeBlock::create([
            'user_id' => $task->user_id,
            'goal_id' => $task->goal_id,
            'date' => $date,
            'title' => $task->title,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'latest_start_time' => $start->copy()->addMinutes(min(15, max(1, $minutes)))->format('H:i:s'),
            'objective' => $task->description,
            'category' => $task->lifeArea?->name ?? 'Task',
            'status' => TimeBlockStatus::Scheduled,
        ]);

        $taskUpdates = [
            'scheduled_time' => $start->format('H:i:s'),
            'block_end_time' => $end->format('H:i:s'),
            'estimated_minutes' => $minutes,
            'daily_time_block' => $repeatDaily,
        ];

        if ($repeatDaily) {
            $taskUpdates['due_date'] = $task->due_date ?? $date;
        }

        $task->update($taskUpdates);

        return $block;
    }
}
