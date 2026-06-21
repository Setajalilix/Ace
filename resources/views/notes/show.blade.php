@extends('layouts.app')

@section('title', ($note->title ?: 'Note') . ' — LifeOS')

@section('content')
<div class="mb-6">
    <a href="{{ route('notes.index') }}" class="text-sm text-zinc-500">&larr; Notes</a>
    <h1 class="text-2xl font-semibold mt-2">{{ $note->title ?: 'Untitled' }}</h1>
    <p class="text-xs text-zinc-500">{{ $note->type->label() }}</p>
</div>
<div class="card prose prose-zinc dark:prose-invert max-w-none">
    {!! $html !!}
</div>
@endsection
