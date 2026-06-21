<?php

namespace App\Domains\Shutdown\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShutdownLog extends Model
{
    protected $fillable = ['user_id', 'date', 'checklist', 'completed_at'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'checklist' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultChecklist(): array
    {
        return [
            'review_inbox' => false,
            'review_tasks' => false,
            'review_events' => false,
            'plan_tomorrow' => false,
            'write_journal' => false,
            'close_day' => false,
        ];
    }

    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }
}
