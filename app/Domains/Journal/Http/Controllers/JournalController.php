<?php

namespace App\Domains\Journal\Http\Controllers;

use App\Domains\Journal\Enums\JournalType;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Journal\Http\Requests\StoreJournalRequest;
use App\Domains\Journal\Models\JournalEntry;
use App\Shared\Calendar\JalaliDateService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request, JalaliDateService $jalali)
    {
        $user = $request->user();
        $today = today();

        $morning = $user->journalEntries()
            ->whereDate('date', $today)
            ->where('type', JournalType::Morning)
            ->first();

        $evening = $user->journalEntries()
            ->whereDate('date', $today)
            ->where('type', JournalType::Evening)
            ->first();

        $entries = $user->journalEntries()->latest('date')->paginate(15);

        return view('journal.index', [
            'entries' => $entries,
            'morning' => $morning,
            'evening' => $evening,
            'today' => $today,
            'jalaliLabel' => $jalali->format($today),
        ]);
    }

    public function store(StoreJournalRequest $request)
    {
        JournalEntry::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'date' => $request->validated('date'),
                'type' => JournalType::from($request->validated('type')),
            ],
            [
                'content' => $request->validated('content'),
            ]
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Journal saved.']);
        }

        return back()->with('success', 'Journal saved.');
    }
}
