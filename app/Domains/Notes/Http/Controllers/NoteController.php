<?php

namespace App\Domains\Notes\Http\Controllers;

use App\Domains\Notes\Enums\NoteType;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Notes\Http\Requests\StoreNoteRequest;
use App\Domains\Notes\Http\Requests\UpdateNoteRequest;
use App\Domains\Notes\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = $request->user()->notes()
            ->where('archived', false)
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(20);

        return view('notes.index', compact('notes'));
    }

    public function store(StoreNoteRequest $request)
    {
        $request->user()->notes()->create([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'type' => NoteType::from($request->validated('type') ?? 'quick'),
        ]);

        return back()->with('success', 'Note saved.');
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);
        $note->update($request->validated());

        return back()->with('success', 'Note updated.');
    }

    public function destroy(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);
        $note->delete();

        return back()->with('success', 'Note deleted.');
    }

    public function preview(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        return view('notes.show', [
            'note' => $note,
            'html' => Str::markdown($note->content ?? ''),
        ]);
    }
}
