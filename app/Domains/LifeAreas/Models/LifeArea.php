<?php

namespace App\Domains\LifeAreas\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Events\Models\Event;
use App\Domains\Goals\Models\Goal;
use App\Domains\Habits\Models\Habit;
use App\Domains\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LifeArea extends Model
{
    protected $fillable = ['user_id', 'name', 'slug', 'color', 'sort_order'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
