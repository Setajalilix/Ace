<?php

namespace App\Domains\Focus\Http\Controllers;

use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use Illuminate\Http\Request;

class FocusController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = today();

        $currentBlock = TimeBlock::where('user_id', $user->id)
            ->forDate($today)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('start_time')
            ->first();

        $currentTask = Task::forUser($user->id)
            ->where('status', TaskStatus::InProgress)
            ->first()
            ?? Task::forUser($user->id)
                ->dueOn($today)
                ->where('priority', TaskPriority::P1)
                ->where('status', TaskStatus::Pending)
                ->first();

        return view('focus.index', compact('currentBlock', 'currentTask'));
    }
}
