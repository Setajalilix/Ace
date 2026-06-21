<?php

namespace App\Domains\Habits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    protected $fillable = [
        'habit_id', 'date', 'completed', 'spent_minutes', 'count', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
