<?php

namespace App\Domains\DailySuccess\Models;

use App\Domains\Auth\Models\User;
use App\Domains\DailySuccess\Enums\DayResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyScore extends Model
{
    protected $fillable = [
        'user_id', 'date', 'p1_total', 'p1_completed',
        'p2_total', 'p2_completed', 'result',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'result' => DayResult::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
