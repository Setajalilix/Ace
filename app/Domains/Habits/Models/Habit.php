<?php

namespace App\Domains\Habits\Models;

use App\Domains\Auth\Models\User;
use App\Domains\LifeAreas\Models\LifeArea;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'life_area_id', 'title', 'description', 'icon', 'color',
        'type', 'repeat_every', 'frequency', 'target_days',
        'target_minutes', 'target_count', 'daily_increment', 'has_time_block', 'block_time',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'has_time_block' => 'boolean',
            'target_days' => 'array',
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

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function shouldAppearToday(): bool
    {
        $startDate = Carbon::parse($this->start_date);
        $daysPassed = $startDate->diffInDays(today());

        if ($this->frequency === 'weekly' && $this->target_days) {
            return in_array(today()->dayOfWeek, $this->target_days);
        }

        return $daysPassed % $this->repeat_every === 0;
    }

    public function todayTargetMinutes(): int
    {
        if ($this->type !== 'timer') {
            return 0;
        }

        $daysPassed = Carbon::parse($this->start_date)->diffInDays(today());

        return max(0, ($this->target_minutes ?? 0) + ($daysPassed * ($this->daily_increment ?? 0)));
    }

    public function todayTargetCount(): int
    {
        return max(1, (int) ($this->target_count ?? 1));
    }

    public function isCounter(): bool
    {
        return $this->type === 'counter';
    }

    public function isTimer(): bool
    {
        return $this->type === 'timer';
    }
}
