<?php

namespace App\Domains\TimeBlocks\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Goals\Models\Goal;
use App\Domains\TimeBlocks\Enums\TimeBlockStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeBlock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'goal_id', 'date', 'title', 'start_time', 'end_time',
        'latest_start_time', 'category', 'objective', 'status',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => TimeBlockStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
