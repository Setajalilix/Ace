<?php

namespace App\Domains\Inbox\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboxItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'body', 'processed_at', 'converted_type', 'converted_id'];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function converted()
    {
        return $this->morphTo();
    }

    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }
}
