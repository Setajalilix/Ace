<?php

namespace App\Domains\Notes\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Notes\Enums\NoteType;
use App\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'title', 'content', 'pinned', 'archived',
        'notable_type', 'notable_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => NoteType::class,
            'pinned' => 'boolean',
            'archived' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
