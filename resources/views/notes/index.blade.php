@extends('layouts.app')

@section('title', 'Notes — LifeOS')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Notes</h1>
</div>

<div class="card mb-6">
    <form method="GET" action="{{ route('notes.index') }}" class="flex gap-2 mb-4">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search notes..." class="input flex-1">
        <button type="submit" class="btn-secondary">Search</button>
    </form>
    <form method="POST" action="{{ route('notes.store') }}" class="space-y-3">
        @csrf
        <input type="text" name="title" placeholder="Title (optional)" class="input">
        <textarea name="content" class="input min-h-24" placeholder="Write a note in markdown..." required></textarea>
        <div class="flex gap-2 items-center">
            <select name="type" class="input w-auto">
                <option value="quick">Quick Note</option>
                <option value="permanent">Permanent Note</option>
                <option value="project">Project Note</option>
            </select>
            <button type="submit" class="btn-primary">Save note</button>
        </div>
    </form>
</div>

<div class="space-y-3">
    @forelse($notes as $note)
        <div class="card">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @if($note->pinned)<span class="text-xs text-amber-500">Pinned</span>@endif
                        <span class="text-xs text-zinc-500">{{ $note->type->label() }}</span>
                    </div>
                    @if($note->title)<h3 class="font-medium">{{ $note->title }}</h3>@endif
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 whitespace-pre-wrap">{{ Str::limit($note->content, 200) }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('notes.show', $note) }}" class="btn-secondary text-xs">View</a>
                    <form method="POST" action="{{ route('notes.destroy', $note) }}">@csrf @method('DELETE')
                        <button class="text-xs text-red-500">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center py-12 text-zinc-400">No notes yet.</div>
    @endforelse
    {{ $notes->links() }}
</div>
@endsection
