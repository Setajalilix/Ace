<?php

namespace App\Domains\Reviews\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReview extends Model
{
    protected $fillable = ['user_id', 'month', 'content', 'stats_snapshot'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'stats_snapshot' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultContent(): array
    {
        return [
            'lessons_learned' => '',
            'reflection' => '',
        ];
    }
}
