<?php

namespace App\Domains\Tasks\Http\Controllers;

use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Tasks\Http\Requests\UpdateKanbanRequest;
use App\Domains\Tasks\Models\Task;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
    public function kanban(Request $request)
    {
        $showDone = $request->boolean('show_done');

        $tasks = $request->user()->tasks()
            ->rootTasks()
            ->with(['lifeArea', 'goal'])
            ->orderBy('kanban_sort')
            ->get()
            ->filter(fn (Task $task) => $this->visibleOnBoard($task, $showDone))
            ->groupBy(fn ($task) => $task->kanban_column->value);

        return view('tasks.kanban', compact('tasks', 'showDone'));
    }

    public function updateKanban(UpdateKanbanRequest $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $column = KanbanColumn::from($request->validated('kanban_column'));
        $updates = [
            'kanban_column' => $column,
            'kanban_sort' => $request->validated('kanban_sort') ?? 0,
        ];

        if ($column === KanbanColumn::Doing) {
            $updates['status'] = TaskStatus::InProgress;
        } elseif ($column === KanbanColumn::Done) {
            $updates['status'] = TaskStatus::Completed;
            $updates['completed_at'] = $task->completed_at ?? now();
        } elseif ($column !== KanbanColumn::Done && $task->status === TaskStatus::InProgress) {
            $updates['status'] = TaskStatus::Pending;
        }

        $task->update($updates);

        return response()->json(['success' => true]);
    }

    private function visibleOnBoard(Task $task, bool $showDone): bool
    {
        if ($task->kanban_column !== KanbanColumn::Done) {
            return true;
        }

        if ($showDone) {
            return true;
        }

        return $task->completed_at && $task->completed_at->gte(now()->startOfWeek());
    }
}
