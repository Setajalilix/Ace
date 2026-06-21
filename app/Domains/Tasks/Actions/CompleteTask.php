<?php

namespace App\Domains\Tasks\Actions;

use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\Tasks\Models\Task;
use App\Domains\Auth\Models\User;
use App\Domains\DailySuccess\Services\DailySuccessService;
use Carbon\Carbon;

class CompleteTask
{
    public function __construct(private DailySuccessService $dailySuccess) {}

    public function execute(Task $task, User $user): Task
    {
        $task->update([
            'status' => TaskStatus::Completed,
            'kanban_column' => KanbanColumn::Done,
            'completed_at' => now(),
        ]);

        if ($task->due_date) {
            $this->dailySuccess->record($user, Carbon::parse($task->due_date));
        }

        return $task->fresh();
    }
}
