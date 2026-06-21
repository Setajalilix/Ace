@extends('layouts.app')
@section('title', 'Board — '.config('app.name'))
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title">Task Board</h1>
        <p class="text-sm text-[#A8958B] mt-1">Done tasks from this week only · drag to move</p>
    </div>
    <div class="flex gap-2">
        @if($showDone)
            <a href="{{ route('board.index') }}" class="btn-secondary text-xs">Hide older done</a>
        @else
            <a href="{{ route('board.index', ['show_done' => 1]) }}" class="btn-secondary text-xs">Show all done</a>
        @endif
    </div>
</div>
<x-kanban-board :tasks="$tasks" />
@endsection
