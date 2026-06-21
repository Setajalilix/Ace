<?php

namespace App\Domains\Events\Models;

use App\Domains\Events\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventOccurrence extends Model
{
    protected $fillable = ['event_id', 'scheduled_at', 'actual_start', 'status'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'actual_start' => 'datetime',
            'status' => EventStatus::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
