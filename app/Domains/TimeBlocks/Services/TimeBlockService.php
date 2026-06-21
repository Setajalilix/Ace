<?php

namespace App\Domains\TimeBlocks\Services;

use App\Domains\TimeBlocks\Enums\TimeBlockStatus;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;

class TimeBlockService
{
    public function start(TimeBlock $block): TimeBlock
    {
        $block->update([
            'status' => TimeBlockStatus::InProgress,
            'started_at' => now(),
        ]);

        return $block->fresh();
    }

    public function complete(TimeBlock $block): TimeBlock
    {
        $block->update([
            'status' => TimeBlockStatus::Completed,
            'completed_at' => now(),
        ]);

        return $block->fresh();
    }

    public function checkMissedBlocks(User $user, Carbon $date): void
    {
        $blocks = TimeBlock::where('user_id', $user->id)
            ->forDate($date)
            ->where('status', TimeBlockStatus::Scheduled)
            ->whereNotNull('latest_start_time')
            ->get();

        foreach ($blocks as $block) {
            $latestStart = Carbon::parse($date->toDateString().' '.$block->latest_start_time);

            if (now()->gt($latestStart) && ! $block->started_at) {
                $block->update(['status' => TimeBlockStatus::Missed]);
            }
        }
    }

    public function reschedule(TimeBlock $block, Carbon $newDate, string $startTime, string $endTime): TimeBlock
    {
        $block->update([
            'date' => $newDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => TimeBlockStatus::Scheduled,
            'started_at' => null,
            'completed_at' => null,
        ]);

        return $block->fresh();
    }

    public function skip(TimeBlock $block): TimeBlock
    {
        $block->update(['status' => TimeBlockStatus::Skipped]);

        return $block->fresh();
    }
}
