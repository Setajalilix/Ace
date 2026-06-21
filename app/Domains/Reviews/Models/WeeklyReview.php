<?php

namespace App\Domains\Reviews\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReview extends Model
{
    protected $fillable = ['user_id', 'week_start', 'content'];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'content' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultContent(): array
    {
        return [
            'inbox_notes' => '',
            'goal_notes' => '',
            'project_notes' => '',
            'habit_notes' => '',
            'calendar_notes' => '',
            'wins' => '',
            'problems' => '',
            'improvements' => '',
        ];
    }
}
