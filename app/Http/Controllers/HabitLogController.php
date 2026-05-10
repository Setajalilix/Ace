<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function toggle(Habit $habit)
    {
        HabitLog::updateOrCreate(
            [
                'habit_id' => $habit->id,
                'date' => today(),
            ],
            [
                'completed' => true,
            ]
        );

        return back();
    }

    public function saveTimer(Request $request, Habit $habit)
    {
        HabitLog::updateOrCreate(
            [
                'habit_id' => $habit->id,
                'date' => today(),
            ],
            [
                'spent_minutes' => $request->minutes,
                'completed' => true,
            ]
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
