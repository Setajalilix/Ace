<?php

namespace App\Domains\Auth\Models;

use App\Domains\DailySuccess\Models\DailyScore;
use App\Domains\Events\Models\Event;
use App\Domains\Goals\Models\Goal;
use App\Domains\Habits\Models\Habit;
use App\Domains\Inbox\Models\InboxItem;
use App\Domains\Journal\Models\JournalEntry;
use App\Domains\LifeAreas\Models\LifeArea;
use App\Domains\Notes\Models\Note;
use App\Domains\Shutdown\Models\ShutdownLog;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Shared\Models\Tag;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function lifeAreas(): HasMany
    {
        return $this->hasMany(LifeArea::class);
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

    public function inboxItems(): HasMany
    {
        return $this->hasMany(InboxItem::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function timeBlocks(): HasMany
    {
        return $this->hasMany(TimeBlock::class);
    }

    public function shutdownLogs(): HasMany
    {
        return $this->hasMany(ShutdownLog::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function dailyScores(): HasMany
    {
        return $this->hasMany(DailyScore::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
