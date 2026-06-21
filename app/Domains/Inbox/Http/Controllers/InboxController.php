<?php

namespace App\Domains\Inbox\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Inbox\Http\Requests\QuickCaptureRequest;
use App\Domains\Inbox\Models\InboxItem;
use App\Domains\Inbox\Services\InboxService;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->inboxItems()->latest()->paginate(20);

        return view('inbox.index', compact('items'));
    }

    public function quickCapture(QuickCaptureRequest $request, InboxService $inbox)
    {
        $inbox->capture($request->user(), $request->validated('body'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Captured to inbox.');
    }

    public function destroy(Request $request, InboxItem $inboxItem)
    {
        abort_unless($inboxItem->user_id === $request->user()->id, 403);
        $inboxItem->delete();

        return back()->with('success', 'Inbox item removed.');
    }

    public function convertTask(Request $request, InboxItem $inboxItem, InboxService $inbox)
    {
        abort_unless($inboxItem->user_id === $request->user()->id, 403);

        $inbox->convertToTask($inboxItem, ['title' => $inboxItem->body]);

        return redirect()->route('tasks.index')->with('success', 'Converted to task.');
    }
}
