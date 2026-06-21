<?php

namespace App\Domains\Goals\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Goals\Enums\GoalType;
use App\Domains\LifeAreas\Models\LifeArea;
use App\Domains\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'life_area_id', 'title', 'description', 'why',
        'success_criteria', 'type', 'target_date', 'progress',
    ];

    protected function casts(): array
    {
        return [
            'type' => GoalType::class,
            'target_date' => 'date',
            'progress' => 'integer',
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

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
