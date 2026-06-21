@extends('layouts.app')

@section('title', 'Monthly Review — LifeOS')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Monthly Review</h1>
<p class="text-sm text-zinc-500 mb-6">{{ $date->format('F Y') }} · <x-jalali-date :date="$date" /></p>

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <div class="card"><p class="text-xs text-zinc-500">Success Rate</p><p class="text-2xl font-semibold">{{ $statistics['success_rate'] }}%</p></div>
    <div class="card"><p class="text-xs text-zinc-500">Goals Completed</p><p class="text-2xl font-semibold">{{ $statistics['completed_goals'] }}</p></div>
    <div class="card"><p class="text-xs text-zinc-500">Projects Completed</p><p class="text-2xl font-semibold">{{ $statistics['completed_projects'] }}</p></div>
</div>

<div class="card max-w-2xl">
    <form method="POST" action="{{ route('reviews.monthly.save') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="month" value="{{ $date->format('Y-m') }}">
        <div><label class="block text-sm font-medium mb-1">Lessons learned</label>
            <textarea name="content[lessons_learned]" class="input min-h-24">{{ $review->content['lessons_learned'] ?? '' }}</textarea>
        </div>
        <div><label class="block text-sm font-medium mb-1">Reflection</label>
            <textarea name="content[reflection]" class="input min-h-24">{{ $review->content['reflection'] ?? '' }}</textarea>
        </div>
        <button type="submit" class="btn-primary">Save review</button>
    </form>
</div>
@endsection
