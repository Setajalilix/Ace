<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'color',
        'type',
        'repeat_every',
        'target_minutes',
        'daily_increment',
        'has_time_block',
        'block_time',
        'start_date',
    ];
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'has_time_block' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }

    public function shouldAppearToday(): bool
    {
        $startDate = \Carbon\Carbon::parse($this->start_date);

        $daysPassed = $startDate->diffInDays(today());

        return $daysPassed % $this->repeat_every === 0;
    }

    public function todayTargetMinutes(): int
    {
        if ($this->type !== 'timer') {
            return 0;
        }

        $daysPassed =
            \Carbon\Carbon::parse($this->start_date)
                ->diffInDays(today());

        return max(
            0,
            ($this->target_minutes ?? 0) +
            ($daysPassed * ($this->daily_increment ?? 0))
        );
    }

    public function streak(): int
    {
        $days = 0;

        $date = today();

        while (true) {

            $log = $this->logs()
                ->whereDate('date', $date)
                ->first();

            if (!$log || !$log->completed) {
                break;
            }

            $days++;

            $date->subDay();
        }

        return $days;
    }

    public function successRate(): int
    {
        $total = $this->logs()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->logs()
            ->where('completed', true)
            ->count();

        return round(($completed / $total) * 100);
    }
}
