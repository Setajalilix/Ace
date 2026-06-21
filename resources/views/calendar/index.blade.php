@extends('layouts.app')
@section('title', 'Calendar — '.config('app.name'))
@section('content')
<div class="flex flex-col gap-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="page-title">Calendar</h1>
            <p class="text-sm text-[#A8958B] mt-1">{{ $jalaliLabel }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => $prevDate->toDateString()]) }}" class="btn-secondary px-3">&larr;</a>
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => today()->toDateString()]) }}" class="btn-secondary text-xs">Today</a>
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => $nextDate->toDateString()]) }}" class="btn-secondary px-3">&rarr;</a>
            @foreach(['month','week','day'] as $v)
                <a href="{{ route('calendar.index', ['view' => $v, 'date' => $date->toDateString()]) }}"
                   class="{{ $view === $v ? 'btn-primary' : 'btn-secondary' }} text-xs">{{ ucfirst($v) }}</a>
            @endforeach
        </div>
    </div>
</div>

@if($view === 'month')
    <div class="card overflow-hidden p-0">
        <div class="grid grid-cols-7 gap-px bg-[#EDE5DA]">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="bg-[#FAF7F2] p-2 text-xs font-semibold text-[#A8958B] text-center">{{ $day }}</div>
            @endforeach
            @foreach($weeks as $week)
                @foreach($week as $day)
                    @php
                        $score = $scores->get($day->day);
                        $isCurrentMonth = $day->month === $date->month;
                        $dayTasks = $tasks->filter(fn ($t) => $t->due_date?->toDateString() === $day->toDateString());
                        $dayEvents = isset($recurrence) ? $recurrence->forDate($events, $day) : collect();
                    @endphp
                    <a href="{{ route('calendar.index', ['view' => 'day', 'date' => $day->toDateString()]) }}"
                       class="bg-white p-2 min-h-[88px] sm:min-h-[100px] block transition-all hover:bg-[#FAF7F2] {{ !$isCurrentMonth ? 'opacity-40' : '' }} {{ $day->isToday() ? 'ring-2 ring-inset ring-[#C47D5A] bg-[#FEF3E0]/40' : '' }}">
                        <div class="flex items-start justify-between gap-1">
                            <div>
                                <span class="text-sm {{ $day->isToday() ? 'font-bold text-[#C47D5A]' : 'text-[#3D3229]' }}">{{ $day->day }}</span>
                                @if($isCurrentMonth)
                                    <p class="text-[10px] text-[#A8958B]">{{ app(\App\Shared\Calendar\JalaliDateService::class)->format($day, 'j F') }}</p>
                                @endif
                            </div>
                            @if($score && $isCurrentMonth)
                                @php $dotClass = match($score->result->value) { 'success' => 'bg-[#7BAE7F]', 'average' => 'bg-[#E6A23C]', default => 'bg-[#E8836B]' }; @endphp
                                <span class="w-2 h-2 rounded-full {{ $dotClass }} mt-1"></span>
                            @endif
                        </div>
                        @if($isCurrentMonth && ($dayTasks->count() || $dayEvents->count()))
                            <div class="mt-1 space-y-0.5">
                                @foreach($dayTasks->take(2) as $t)
                                    <p class="text-[10px] truncate text-[#6B5B4F] bg-[#F3EDE4] rounded px-1">{{ $t->title }}</p>
                                @endforeach
                                @foreach($dayEvents->take(1) as $e)
                                    <p class="text-[10px] truncate text-[#6B9BD1]">{{ $e->title }}</p>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </div>
        <div class="flex gap-4 p-4 text-xs text-[#A8958B] border-t border-[#EDE5DA]">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#7BAE7F]"></span> Successful</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#E6A23C]"></span> Average</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#E8836B]"></span> Failed</span>
        </div>
    </div>
@elseif($view === 'week')
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
        @foreach($days as $day)
            <a href="{{ route('calendar.index', ['view' => 'day', 'date' => $day->toDateString()]) }}"
               class="card min-h-[120px] block hover:shadow-md transition-shadow {{ $day->isToday() ? 'ring-2 ring-[#C47D5A]' : '' }}">
                <p class="text-xs font-semibold {{ $day->isToday() ? 'text-[#C47D5A]' : 'text-[#3D3229]' }}">{{ $day->format('D j') }}</p>
                <p class="text-[10px] text-[#A8958B]"><x-jalali-date :date="$day" /></p>
                @foreach($tasks->filter(fn ($t) => $t->due_date && $t->due_date->toDateString() === $day->toDateString())->take(3) as $task)
                    <p class="text-xs mt-1 truncate text-[#6B5B4F]">{{ $task->title }}</p>
                @endforeach
            </a>
        @endforeach
    </div>
@else
    <div class="card mb-4 {{ $date->isToday() ? 'ring-2 ring-[#C47D5A]' : '' }}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-semibold text-[#3D3229] {{ $date->isToday() ? 'text-[#C47D5A]' : '' }}">
                    {{ $date->format('l, F j, Y') }}
                    @if($date->isToday())<span class="text-xs font-semibold ml-2 px-2 py-0.5 rounded-full bg-[#C47D5A] text-white">Today</span>@endif
                </h2>
                <p class="text-sm text-[#A8958B] mt-1"><x-jalali-date :date="$date" /></p>
            </div>
        </div>
    </div>
    <div class="grid lg:grid-cols-3 gap-4">
        <div class="card lg:col-span-2">
            <h3 class="section-label mb-3">Tasks</h3>
            @forelse($tasks as $task)<x-task-row :task="$task" />@empty<p class="text-sm text-[#A8958B]">No tasks.</p>@endforelse
        </div>
        <div class="space-y-4">
            <div class="card">
                <h3 class="section-label mb-3">Time Blocks</h3>
                @forelse($timeBlocks as $block)
                    <p class="text-sm py-1 text-[#3D3229]">{{ substr($block->start_time,0,5) }} — {{ $block->title }}</p>
                @empty<p class="text-sm text-[#A8958B]">No blocks.</p>@endforelse
            </div>
            <div class="card">
                <h3 class="section-label mb-3">Events</h3>
                @forelse($events as $event)
                    <p class="text-sm py-1 text-[#3D3229]">{{ $event->starts_at->format('H:i') }} — {{ $event->title }}</p>
                @empty<p class="text-sm text-[#A8958B]">No events.</p>@endforelse
            </div>
        </div>
    </div>
@endif
@endsection
