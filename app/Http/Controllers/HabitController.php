<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    /**
     * Display all habits
     */
    public function index()
    {
        $habits = Habit::latest()->paginate(12);

        return view('habits.index', compact('habits'));
    }

    /**
     * Show create page
     */
    public function create()
    {
        return view('habits.create');
    }

    /**
     * Store new habit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],

            'icon'              => ['nullable', 'string', 'max:50'],
            'color'             => ['required', 'string'],

            'type'              => ['required', 'in:checkbox,timer'],

            'repeat_every'      => ['required', 'integer', 'min:1'],

            'start_date'        => ['required', 'date'],

            'target_minutes'    => ['nullable', 'integer', 'min:0'],
            'daily_increment'   => ['nullable', 'integer', 'min:0'],

            'has_time_block'    => ['nullable', 'boolean'],
            'block_time'        => ['nullable', 'date_format:H:i'],
        ]);

        $validated['has_time_block'] =
            $request->boolean('has_time_block');

        Habit::create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'عادت جدید ساخته شد.');
    }

    /**
     * Show single habit
     */
    public function show(Habit $habit)
    {
        $habit->load([
            'logs' => function ($query) {
                $query->latest();
            }
        ]);

        return view('habits.show', compact('habit'));
    }

    /**
     * Edit page
     */
    public function edit(Habit $habit)
    {
        return view('habits.edit', compact('habit'));
    }

    /**
     * Update habit
     */
    public function update(Request $request, Habit $habit)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],

            'icon'              => ['nullable', 'string', 'max:50'],
            'color'             => ['required', 'string'],

            'type'              => ['required', 'in:checkbox,timer'],

            'repeat_every'      => ['required', 'integer', 'min:1'],

            'start_date'        => ['required', 'date'],

            'target_minutes'    => ['nullable', 'integer', 'min:0'],
            'daily_increment'   => ['nullable', 'integer', 'min:0'],

            'has_time_block'    => ['nullable', 'boolean'],
            'block_time'        => ['nullable', 'date_format:H:i'],
        ]);

        $validated['has_time_block'] =
            $request->boolean('has_time_block');

        $habit->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'عادت بروزرسانی شد.');
    }

    /**
     * Delete habit
     */
    public function destroy(Habit $habit)
    {
        $habit->delete();

        return back()->with(
            'success',
            'عادت حذف شد.'
        );
    }

    /**
     * Toggle checkbox habits
     */
    public function toggle(Habit $habit)
    {
        $today = today()->toDateString();

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date'     => $today,
        ]);

        $log->completed = !$log->completed;

        if ($log->completed) {
            $log->completed_at = now();
        } else {
            $log->completed_at = null;
        }

        $log->save();

        return back();
    }

    /**
     * Save timer progress
     */
    public function saveTimer(Request $request, Habit $habit)
    {
        $validated = $request->validate([
            'spent_minutes' => ['required', 'integer', 'min:0'],
        ]);

        $today = today()->toDateString();

        $log = HabitLog::firstOrCreate([
            'habit_id' => $habit->id,
            'date'     => $today,
        ]);

        $log->spent_minutes = $validated['spent_minutes'];

        $target =
            $habit->todayTargetMinutes();

        $log->completed =
            $validated['spent_minutes'] >= $target;

        if ($log->completed) {
            $log->completed_at = now();
        }

        $log->save();

        return response()->json([
            'success' => true,
            'completed' => $log->completed,
        ]);
    }

    /**
     * Dashboard page
     */
    public function dashboard()
    {
        $today = today();

        $habits = Habit::with([
            'logs' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }
        ])
        ->get()
        ->filter(function ($habit) use ($today) {

            $daysPassed =
                Carbon::parse($habit->start_date)
                    ->diffInDays($today);

            return $daysPassed %
                $habit->repeat_every === 0;
        });

        $completedCount = 0;

        foreach ($habits as $habit) {

            $log = $habit->logs->first();

            if (!$log) {
                continue;
            }

            if ($habit->type === 'checkbox') {

                if ($log->completed) {
                    $completedCount++;
                }

            } else {

                if (
                    $log->spent_minutes >=
                    $habit->todayTargetMinutes()
                ) {
                    $completedCount++;
                }
            }
        }

        $completionRate =
            count($habits) > 0
                ? round(
                    ($completedCount / count($habits)) * 100
                )
                : 0;

        $focusMinutes =
            HabitLog::whereDate('date', $today)
                ->sum('spent_minutes');

        return view('dashboard', [
            'habits' => $habits,
            'completionRate' => $completionRate,
            'focusMinutes' => $focusMinutes,
            'streak' => $this->calculateStreak(),
        ]);
    }

    /**
     * Calculate streak
     */
    private function calculateStreak(): int
    {
        $days = 0;

        $date = today();

        while (true) {

            $logs = HabitLog::whereDate(
                'date',
                $date
            )->get();

            if ($logs->count() === 0) {
                break;
            }

            $completed = $logs->every(function ($log) {
                return $log->completed;
            });

            if (!$completed) {
                break;
            }

            $days++;

            $date->subDay();
        }

        return $days;
    }
}
