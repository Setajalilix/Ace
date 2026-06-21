<?php

namespace App\Domains\Tasks\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Goals\Models\Goal;
use App\Domains\LifeAreas\Models\LifeArea;
use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'life_area_id', 'goal_id', 'parent_id', 'title',
        'description', 'priority', 'status', 'kanban_column', 'kanban_sort',
        'due_date', 'scheduled_time', 'block_end_time', 'daily_time_block', 'estimated_minutes', 'recurrence_rule',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'kanban_column' => KanbanColumn::class,
            'daily_time_block' => 'boolean',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lifeArea(): BelongsTo
    {
        return $this->belongsTo(LifeArea::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueOn($query, $date)
    {
        $day = $date instanceof \Carbon\Carbon
            ? $date->toDateString()
            : \Carbon\Carbon::parse($date)->toDateString();

        return $query->whereDate('due_date', $day);
    }

    public function scopeRootTasks($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeIncompleteFirst($query)
    {
        return $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('priority')
            ->orderByDesc('updated_at');
    }
}
