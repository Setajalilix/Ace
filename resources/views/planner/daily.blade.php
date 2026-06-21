@extends('layouts.app')
@section('title', 'Today — '.config('app.name'))
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}</h1>
        <p class="text-sm text-[#A8958B] mt-1">{{ $dateLabel }} · <x-jalali-date :date="$date" /></p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $resultStyles = match($dailyScore->result->value) {
                'success' => 'bg-[#E8F5E9] text-[#2E7D32] border-[#C8E6C9]',
                'average' => 'bg-[#FEF3E0] text-[#B7791F] border-[#F5DFB8]',
                default => 'bg-[#FEE8E4] text-[#C0392B] border-[#F5C4BC]',
            };
        @endphp
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $resultStyles }}">{{ $dailyScore->result->label() }}</span>
        <button type="button" @click="openTaskCreate()" class="btn-primary text-sm"><x-icon name="plus" class="w-4 h-4" /> Add task</button>
        <a href="{{ route('focus.index') }}" class="btn-secondary text-sm"><x-icon name="play" class="w-4 h-4" /> Focus</a>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        @php
            $allTasksDone = $p1Tasks->isEmpty() && $p2Tasks->isEmpty() && $p3Tasks->isEmpty();
        @endphp

        @if($allTasksDone)
            <x-today-all-done />
        @else
            @foreach([
                ['tasks' => $p1Tasks, 'label' => 'Must complete today', 'icon' => 'fire'],
                ['tasks' => $p2Tasks, 'label' => 'Should complete', 'icon' => 'bolt'],
                ['tasks' => $p3Tasks, 'label' => 'Optional', 'icon' => 'leaf'],
            ] as $group)
                <section class="card-flat">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon :name="$group['icon']" class="w-4 h-4 text-[#C47D5A]" />
                        <h2 class="section-label">{{ $group['label'] }}</h2>
                    </div>
                    @foreach($group['tasks'] as $task)
                        <x-task-row :task="$task" />
                    @endforeach
                </section>
            @endforeach
        @endif
    </div>

    <div class="space-y-5">
        <section class="card-flat">
            <div class="flex items-center justify-between mb-3">
                <h2 class="section-label">Habits</h2>
                <a href="{{ route('habits.index') }}" class="text-xs text-[#C47D5A] hover:underline">All habits</a>
            </div>
            <div data-today-habits>
            @forelse($habits as $habit)
                <div class="habit-today-row transition-all duration-300" data-habit-id="{{ $habit->id }}">
                    <x-habit-item :habit="$habit" />
                </div>
            @empty
                <p class="text-sm text-[#A8958B] habit-today-empty">No habits due.</p>
            @endforelse
            </div>
        </section>

        <section class="card-flat">
            <div class="flex items-center justify-between mb-3">
                <h2 class="section-label">Events</h2>
                <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-event-create'))" class="text-xs text-[#C47D5A] hover:underline">Add event</button>
            </div>
            @forelse($events as $event)
                @php
                    $eventData = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'location' => $event->location,
                        'start_date' => $event->starts_at->toDateString(),
                        'start_time' => $event->starts_at->format('H:i'),
                        'end_date' => $event->ends_at?->toDateString(),
                        'end_time' => $event->ends_at?->format('H:i'),
                        'life_area_id' => $event->life_area_id,
                        'recurrence' => $event->recurrence_rule ? (json_decode($event->recurrence_rule, true)['type'] ?? 'none') : 'none',
                        'status' => $event->status->value,
                    ];
                @endphp
                <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-event-edit', { detail: @js($eventData) }))"
                        class="w-full py-2 flex items-center gap-2 text-sm text-left hover:bg-[#FAF7F2] rounded-lg px-1 -mx-1 transition-colors">
                    <x-icon name="calendar" class="w-4 h-4 text-[#6B9BD1] shrink-0" />
                    <span class="truncate font-medium text-[#3D3229]">{{ $event->title }}</span>
                    <span class="text-xs text-[#A8958B] ml-auto shrink-0">{{ $event->starts_at->format('H:i') }}</span>
                </button>
            @empty
                <p class="text-sm text-[#A8958B]">No events today.</p>
            @endforelse
        </section>

        <section class="card-flat">
            <div class="flex items-center justify-between mb-2">
                <h2 class="section-label">Journal</h2>
                <a href="{{ route('journal.index') }}" class="text-xs text-[#C47D5A] hover:underline">Open journal →</a>
            </div>
            <p class="text-sm text-[#A8958B]">Morning & evening reflections live on the Journal page.</p>
        </section>
    </div>
</div>
@endsection
