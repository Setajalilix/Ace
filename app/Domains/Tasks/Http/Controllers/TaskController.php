<?php

namespace App\Domains\Tasks\Http\Controllers;

use App\Domains\Tasks\Actions\CompleteTask;
use App\Domains\Tasks\Actions\ScheduleTaskAsTimeBlock;
use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Tasks\Http\Requests\ScheduleTaskTimeBlockRequest;
use App\Domains\Tasks\Http\Requests\StoreTaskRequest;
use App\Domains\Tasks\Http\Requests\UpdateTaskRequest;
use App\Domains\Tasks\Models\Task;
use App\Domains\Tasks\Services\TaskSchedulerService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, TaskSchedulerService $scheduler)
    {
        $scheduler->process($request->user());

        $query = $request->user()->tasks()
            ->rootTasks()
            ->with(['goal', 'lifeArea']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', TaskStatus::Completed);
        }
        if ($request->filled('priority')) {
            $query->where('priority', (int) $request->priority);
        }
        if ($request->filled('life_area_id')) {
            $query->where('life_area_id', $request->life_area_id);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        $tasks = $query->incompleteFirst()->paginate(20)->withQueryString();

        $lifeAreas = $request->user()->lifeAreas()->get();

        return view('tasks.index', compact('tasks', 'lifeAreas'));
    }

    public function create(Request $request)
    {
        $goals = $request->user()->goals()->get();
        $lifeAreas = $request->user()->lifeAreas()->get();

        return view('tasks.create', compact('goals', 'lifeAreas'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'priority' => TaskPriority::from((int) $request->validated('priority')),
            'status' => TaskStatus::Pending,
            'kanban_column' => KanbanColumn::Backlog,
            'due_date' => $request->dueDate()?->toDateString(),
            'scheduled_time' => $request->validated('scheduled_time'),
            'estimated_minutes' => $request->validated('estimated_minutes'),
            'goal_id' => $request->validated('goal_id'),
            'life_area_id' => $request->validated('life_area_id'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task created.',
                'task' => $this->taskPayload($task->fresh(['lifeArea', 'goal'])),
            ]);
        }

        return redirect()->back()->with('success', 'Task created.');
    }

    public function edit(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $goals = $request->user()->goals()->get();
        $lifeAreas = $request->user()->lifeAreas()->get();

        return view('tasks.edit', compact('task', 'goals', 'lifeAreas'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $status = TaskStatus::from($request->validated('status'));
        $kanban = $task->kanban_column;

        if ($status === TaskStatus::InProgress) {
            $kanban = KanbanColumn::Doing;
        } elseif ($status === TaskStatus::Completed) {
            $kanban = KanbanColumn::Done;
        }

        $task->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'priority' => TaskPriority::from((int) $request->validated('priority')),
            'status' => $status,
            'kanban_column' => $kanban,
            'due_date' => $request->dueDate()?->toDateString(),
            'scheduled_time' => $request->validated('scheduled_time'),
            'estimated_minutes' => $request->validated('estimated_minutes'),
            'goal_id' => $request->validated('goal_id'),
            'life_area_id' => $request->validated('life_area_id'),
            'completed_at' => $status === TaskStatus::Completed ? ($task->completed_at ?? now()) : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task updated.',
                'task' => $this->taskPayload($task->fresh(['lifeArea', 'goal'])),
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403);
        $task->delete();

        return back()->with('success', 'Task deleted.');
    }

    public function complete(Request $request, Task $task, CompleteTask $completeTask)
    {
        abort_unless($task->user_id === $request->user()->id, 403);
        $completeTask->execute($task, $request->user());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Task completed.', 'completed' => true]);
        }

        return back()->with('success', 'Task completed.');
    }

    public function start(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->update([
            'status' => TaskStatus::InProgress,
            'kanban_column' => KanbanColumn::Doing,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Task started.', 'status' => 'in_progress']);
        }

        return back()->with('success', 'Task started.');
    }

    public function toTimeBlock(ScheduleTaskTimeBlockRequest $request, Task $task, ScheduleTaskAsTimeBlock $action)
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $block = $action->execute(
            $task,
            $request->blockDate(),
            $request->validated('start_time'),
            $request->validated('end_time'),
            $request->boolean('repeat_daily'),
        );

        $message = sprintf(
            'Time block set: %s · %s–%s',
            $block->date->format('M j'),
            substr($block->start_time, 0, 5),
            substr($block->end_time, 0, 5),
        );

        if ($request->boolean('repeat_daily')) {
            $message .= ' (repeats daily at this time)';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function taskPayload(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority->value,
            'status' => $task->status->value,
            'life_area_id' => $task->life_area_id,
            'goal_id' => $task->goal_id,
            'due_date' => $task->due_date?->toDateString(),
            'estimated_minutes' => $task->estimated_minutes,
            'scheduled_time' => $task->scheduled_time ? substr($task->scheduled_time, 0, 5) : null,
            'completed' => $task->isCompleted(),
        ];
    }
}
