<?php

namespace App\Domains\Habits\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Habits\Http\Requests\StoreHabitRequest;
use App\Domains\Habits\Http\Requests\UpdateHabitRequest;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Services\HabitStatsService;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function __construct(private HabitStatsService $habitStats) {}

    public function index(Request $request)
    {
        $habits = $request->user()->habits()
            ->with(['lifeArea', 'logs' => fn ($q) => $q->whereDate('date', today())])
            ->latest()
            ->paginate(12);
        $stats = app(\App\Domains\Habits\Services\HabitStatsService::class);

        $habits->getCollection()->transform(function ($habit) use ($stats) {
            $habit->activityGrid = $stats->activityGrid($habit);

            return $habit;
        });

        return view('habits.index', compact('habits'));
    }

    public function create(Request $request)
    {
        return redirect()->route('habits.index');
    }

    public function store(StoreHabitRequest $request)
    {
        $habit = $request->user()->habits()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'color' => $request->validated('color'),
            'type' => $request->validated('type'),
            'repeat_every' => $request->validated('repeat_every'),
            'start_date' => $request->startDate(),
            'target_minutes' => $request->validated('target_minutes'),
            'target_count' => $request->validated('target_count'),
            'daily_increment' => $request->validated('daily_increment'),
            'life_area_id' => $request->validated('life_area_id') ?: null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Habit created.',
                'habit' => $this->habitPayload($habit),
            ]);
        }

        return redirect()->route('habits.index')->with('success', 'Habit created.');
    }

    public function show(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $habit->load([
            'lifeArea',
            'logs' => fn ($q) => $q->latest()->limit(30),
        ]);

        return view('habits.show', [
            'habit' => $habit,
            'streak' => $this->habitStats->currentStreak($habit),
            'longestStreak' => $this->habitStats->longestStreak($habit),
            'consistency' => $this->habitStats->monthlyConsistency($habit, today()->year, today()->month),
            'activityGrid' => $this->habitStats->activityGrid($habit),
        ]);
    }

    public function edit(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        return redirect()->route('habits.index');
    }

    public function update(UpdateHabitRequest $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $habit->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'color' => $request->validated('color'),
            'type' => $request->validated('type'),
            'repeat_every' => $request->validated('repeat_every'),
            'start_date' => $request->startDate(),
            'target_minutes' => $request->validated('target_minutes'),
            'target_count' => $request->validated('target_count'),
            'daily_increment' => $request->validated('daily_increment'),
            'life_area_id' => $request->validated('life_area_id') ?: null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Habit updated.',
                'habit' => $this->habitPayload($habit->fresh()),
            ]);
        }

        return redirect()->route('habits.index')->with('success', 'Habit updated.');
    }

    public function destroy(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 403);
        $habit->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Habit deleted.']);
        }

        return redirect()->route('habits.index')->with('success', 'Habit deleted.');
    }

    private function habitPayload(Habit $habit): array
    {
        return [
            'id' => $habit->id,
            'title' => $habit->title,
            'type' => $habit->type,
            'color' => $habit->color ?? '#7BAE7F',
            'repeat_every' => $habit->repeat_every,
            'start_date' => $habit->start_date->toDateString(),
            'target_minutes' => $habit->target_minutes,
            'target_count' => $habit->target_count,
            'daily_increment' => $habit->daily_increment ?? 0,
            'life_area_id' => $habit->life_area_id,
        ];
    }
}
