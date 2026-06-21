@extends('layouts.app')

@section('title', 'Weekly Review — LifeOS')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Weekly Review</h1>
<p class="text-sm text-zinc-500 mb-6">Week of {{ $weekStart->format('M j, Y') }}</p>

<div class="grid sm:grid-cols-4 gap-3 mb-6">
    <div class="card text-center"><p class="text-2xl font-semibold">{{ $summary['unprocessed_inbox'] }}</p><p class="text-xs text-zinc-500">Inbox items</p></div>
    <div class="card text-center"><p class="text-2xl font-semibold">{{ $summary['active_goals'] }}</p><p class="text-xs text-zinc-500">Goals</p></div>
    <div class="card text-center"><p class="text-2xl font-semibold">{{ $summary['active_tasks'] }}</p><p class="text-xs text-[#A8958B]">Active tasks</p></div>
    <div class="card text-center"><p class="text-2xl font-semibold">{{ $summary['habits_count'] }}</p><p class="text-xs text-zinc-500">Habits</p></div>
</div>

<div class="card max-w-2xl">
    <form method="POST" action="{{ route('reviews.weekly.save') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
        @foreach(['inbox_notes'=>'Inbox review','goal_notes'=>'Goal review','project_notes'=>'Project review','habit_notes'=>'Habit review','wins'=>'Wins','problems'=>'Problems','improvements'=>'Improvements'] as $key => $label)
            <div><label class="block text-sm font-medium mb-1">{{ $label }}</label>
                <textarea name="content[{{ $key }}]" class="input min-h-16">{{ $review->content[$key] ?? '' }}</textarea>
            </div>
        @endforeach
        <button type="submit" class="btn-primary">Save review</button>
    </form>
</div>
@endsection
