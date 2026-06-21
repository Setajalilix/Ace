<?php

namespace App\Domains\TimeBlocks\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\TimeBlocks\Http\Requests\RescheduleTimeBlockRequest;
use App\Domains\TimeBlocks\Http\Requests\StoreTimeBlockRequest;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\TimeBlocks\Services\TimeBlockService;
use Illuminate\Http\Request;

class TimeBlockController extends Controller
{
    public function store(StoreTimeBlockRequest $request)
    {
        $request->user()->timeBlocks()->create([
            'title' => $request->validated('title'),
            'date' => $request->blockDate(),
            'start_time' => $request->validated('start_time'),
            'end_time' => $request->validated('end_time'),
            'latest_start_time' => $request->validated('latest_start_time'),
            'category' => $request->validated('category'),
            'objective' => $request->validated('objective'),
            'goal_id' => $request->validated('goal_id'),
        ]);

        return back()->with('success', 'Time block created.');
    }

    public function start(Request $request, TimeBlock $timeBlock, TimeBlockService $service)
    {
        abort_unless($timeBlock->user_id === $request->user()->id, 403);
        $service->start($timeBlock);

        return back()->with('success', 'Block started.');
    }

    public function complete(Request $request, TimeBlock $timeBlock, TimeBlockService $service)
    {
        abort_unless($timeBlock->user_id === $request->user()->id, 403);
        $service->complete($timeBlock);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Block completed.']);
        }

        return back()->with('success', 'Block completed.');
    }

    public function reschedule(RescheduleTimeBlockRequest $request, TimeBlock $timeBlock, TimeBlockService $service)
    {
        abort_unless($timeBlock->user_id === $request->user()->id, 403);

        $service->reschedule(
            $timeBlock,
            $request->blockDate(),
            $request->validated('start_time'),
            $request->validated('end_time'),
        );

        return back()->with('success', 'Block rescheduled.');
    }

    public function destroy(Request $request, TimeBlock $timeBlock)
    {
        abort_unless($timeBlock->user_id === $request->user()->id, 403);
        $timeBlock->delete();

        return back()->with('success', 'Time block deleted.');
    }
}
