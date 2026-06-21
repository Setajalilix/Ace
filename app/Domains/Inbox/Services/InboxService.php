<?php

namespace App\Domains\Inbox\Services;

use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\Goals\Models\Goal;
use App\Domains\Inbox\Models\InboxItem;
use App\Domains\Notes\Models\Note;
use App\Domains\Tasks\Models\Task;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class InboxService
{
    public function capture(User $user, string $body): InboxItem
    {
        return InboxItem::create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }

    public function convertToTask(InboxItem $item, array $data): Task
    {
        $task = Task::create(array_merge($data, [
            'user_id' => $item->user_id,
            'title' => $data['title'] ?? $item->body,
            'priority' => TaskPriority::P2,
            'status' => TaskStatus::Pending,
            'kanban_column' => KanbanColumn::Backlog,
        ]));

        $this->markProcessed($item, $task);

        return $task;
    }

    public function convertToGoal(InboxItem $item, array $data): Goal
    {
        $goal = Goal::create(array_merge($data, [
            'user_id' => $item->user_id,
            'title' => $data['title'] ?? $item->body,
        ]));

        $this->markProcessed($item, $goal);

        return $goal;
    }

    public function convertToNote(InboxItem $item, array $data): Note
    {
        $note = Note::create(array_merge($data, [
            'user_id' => $item->user_id,
            'content' => $data['content'] ?? $item->body,
        ]));

        $this->markProcessed($item, $note);

        return $note;
    }

    public function markProcessed(InboxItem $item, Model $converted): void
    {
        $item->update([
            'processed_at' => now(),
            'converted_type' => $converted->getMorphClass(),
            'converted_id' => $converted->id,
        ]);
    }

    public function unprocessedCount(User $user): int
    {
        return InboxItem::where('user_id', $user->id)->unprocessed()->count();
    }
}
