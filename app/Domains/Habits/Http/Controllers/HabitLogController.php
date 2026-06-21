<?php

namespace App\Domains\Habits\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Habits\Http\Requests\SaveCounterRequest;
use App\Domains\Habits\Http\Requests\SaveTimerRequest;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitLog;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function toggle(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date' => today(),
        ]);

        $log->update([
            'completed' => ! $log->completed,
            'completed_at' => ! $log->completed ? now() : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'completed' => $log->fresh()->completed,
            ]);
        }

        return back();
    }

    public function saveTimer(SaveTimerRequest $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date' => today(),
        ]);

        $target = $habit->todayTargetMinutes();
        $completed = $request->validated('spent_minutes') >= $target;

        $log->update([
            'spent_minutes' => $request->validated('spent_minutes'),
            'completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);

        return response()->json(['success' => true, 'completed' => $completed, 'spent_minutes' => $log->spent_minutes]);
    }

    public function saveCounter(SaveCounterRequest $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date' => today(),
        ]);

        $target = $habit->todayTargetCount();
        $count = $request->validated('count');
        $completed = $count >= $target;

        $log->update([
            'count' => $count,
            'completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);

        $payload = [
            'count' => $count,
            'target' => $target,
            'completed' => $completed,
            'pct' => min(100, ($target > 0 ? ($count / $target) * 100 : 0)),
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back();
    }

    public function incrementCounter(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date' => today(),
        ]);

        $count = ($log->count ?? 0) + 1;
        $target = $habit->todayTargetCount();
        $completed = $count >= $target;

        $log->update([
            'count' => $count,
            'completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'count' => $count,
                'target' => $target,
                'completed' => $completed,
                'pct' => min(100, ($target > 0 ? ($count / $target) * 100 : 0)),
            ]);
        }

        return back();
    }
}
