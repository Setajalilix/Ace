<?php

namespace App\Domains\Goals\Http\Controllers;

use App\Domains\Goals\Enums\GoalType;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Goals\Http\Requests\StoreGoalRequest;
use App\Domains\Goals\Http\Requests\UpdateGoalRequest;
use App\Domains\Goals\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $goals = $request->user()->goals()->with('lifeArea')->latest()->get();

        return view('goals.index', compact('goals'));
    }

    public function create(Request $request)
    {
        return redirect()->route('goals.index');
    }

    public function store(StoreGoalRequest $request)
    {
        $goal = $request->user()->goals()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'why' => $request->validated('why'),
            'success_criteria' => $request->validated('success_criteria'),
            'type' => GoalType::from($request->validated('type')),
            'target_date' => $request->targetDate(),
            'progress' => $request->validated('progress') ?? 0,
            'life_area_id' => $request->validated('life_area_id') ?: null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Goal created.',
                'goal' => $this->goalPayload($goal),
            ]);
        }

        return redirect()->route('goals.index')->with('success', 'Goal created.');
    }

    public function show(Request $request, Goal $goal)
    {
        abort_unless($goal->user_id === $request->user()->id, 403);
        $goal->load(['tasks', 'lifeArea']);

        return view('goals.show', compact('goal'));
    }

    public function edit(Request $request, Goal $goal)
    {
        abort_unless($goal->user_id === $request->user()->id, 403);

        return redirect()->route('goals.index');
    }

    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        abort_unless($goal->user_id === $request->user()->id, 403);

        $goal->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'why' => $request->validated('why'),
            'success_criteria' => $request->validated('success_criteria'),
            'type' => GoalType::from($request->validated('type')),
            'target_date' => $request->targetDate(),
            'progress' => $request->validated('progress') ?? $goal->progress,
            'life_area_id' => $request->validated('life_area_id') ?: null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Goal updated.',
                'goal' => $this->goalPayload($goal->fresh()),
            ]);
        }

        return redirect()->route('goals.index')->with('success', 'Goal updated.');
    }

    public function destroy(Request $request, Goal $goal)
    {
        abort_unless($goal->user_id === $request->user()->id, 403);
        $goal->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Goal deleted.']);
        }

        return back()->with('success', 'Goal deleted.');
    }

    private function goalPayload(Goal $goal): array
    {
        return [
            'id' => $goal->id,
            'title' => $goal->title,
            'why' => $goal->why,
            'success_criteria' => $goal->success_criteria,
            'type' => $goal->type->value,
            'target_date' => $goal->target_date?->toDateString(),
            'progress' => $goal->progress,
            'life_area_id' => $goal->life_area_id,
        ];
    }
}
