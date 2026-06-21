<?php

namespace App\Domains\Journal\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Journal\Enums\JournalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = ['user_id', 'date', 'type', 'content'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => JournalType::class,
            'content' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function morningTemplate(): array
    {
        return [
            'important_today' => '',
            'distractions' => '',
            'desired_feeling' => '',
        ];
    }

    public static function eveningTemplate(): array
    {
        return [
            'went_well' => '',
            'did_not_go_well' => '',
            'improve_tomorrow' => '',
        ];
    }
}
