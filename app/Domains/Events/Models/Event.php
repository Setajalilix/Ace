<?php

namespace App\Domains\Events\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Events\Enums\EventStatus;
use App\Domains\Goals\Models\Goal;
use App\Domains\LifeAreas\Models\LifeArea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'life_area_id', 'goal_id', 'title', 'description',
        'location', 'starts_at', 'ends_at', 'recurrence_rule', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => EventStatus::class,
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

    public function occurrences(): HasMany
    {
        return $this->hasMany(EventOccurrence::class);
    }

    public function recurrenceLabel(): string
    {
        return app(\App\Domains\Events\Services\EventRecurrenceService::class)->label($this->recurrence_rule);
    }
}
