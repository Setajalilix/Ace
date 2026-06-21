@extends('layouts.app')
@section('title', $habit->title.' — '.config('app.name'))
@section('content')
@php
    $color = $habit->color ?? '#7BAE7F';
    $habitData = [
        'id' => $habit->id,
        'title' => $habit->title,
        'type' => $habit->type,
        'color' => $color,
        'repeat_every' => $habit->repeat_every,
        'start_date' => $habit->start_date->toDateString(),
        'target_minutes' => $habit->target_minutes,
        'target_count' => $habit->target_count,
        'daily_increment' => $habit->daily_increment ?? 0,
        'life_area_id' => $habit->life_area_id,
    ];
    $typeLabel = match($habit->type) {
        'checkbox' => 'Daily check-in',
        'timer' => 'Timed session',
        'counter' => 'Count tracker',
        default => ucfirst($habit->type),
    };
@endphp

<div class="habit-show">
    {{-- Hero --}}
    <div class="habit-show-hero fade-in" style="--habit-color: {{ $color }}">
        <div class="habit-show-hero__glow" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="min-w-0">
                <a href="{{ route('habits.index') }}" class="inline-flex items-center gap-1.5 text-sm text-[#A8958B] hover:text-[#C47D5A] transition-colors mb-3">
                    <x-icon name="leaf" class="w-4 h-4" /> Back to habits
                </a>
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-3 h-3 rounded-full shrink-0 ring-4 ring-white/80" style="background: {{ $color }}"></span>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-[#3D3229] tracking-tight">{{ $habit->title }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/70 border border-[#EDE5DA] text-[#6B5B4F]">
                        <x-icon name="repeat" class="w-3.5 h-3.5" /> Every {{ $habit->repeat_every }} day(s)
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/70 border border-[#EDE5DA] text-[#6B5B4F]">{{ $typeLabel }}</span>
                    @if($habit->lifeArea)<x-life-area-badge :area="$habit->lifeArea" />@endif
                </div>
            </div>
            <div class="flex gap-2 shrink-0 self-start sm:mt-8">
                <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-habit-edit', { detail: @js($habitData) }))"
                        class="btn-secondary">
                    <x-icon name="pencil" class="w-4 h-4" /> Edit
                </button>
                <button type="button" @click="deleteHabit({{ $habit->id }})"
                        class="btn-ghost text-[#E05D44] hover:bg-[#FEE8E4] px-3">
                    <x-icon name="x" class="w-4 h-4" /> Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-6 habit-show-stats fade-in">
        <div class="habit-stat-card" style="--stat-accent: {{ $color }}">
            <div class="habit-stat-card__icon">
                <x-icon name="fire" class="w-5 h-5" />
            </div>
            <p class="habit-stat-card__value">{{ $streak }}</p>
            <p class="habit-stat-card__label">Current streak</p>
        </div>
        <div class="habit-stat-card" style="--stat-accent: {{ $color }}">
            <div class="habit-stat-card__icon">
                <x-icon name="bolt" class="w-5 h-5" />
            </div>
            <p class="habit-stat-card__value">{{ $longestStreak }}</p>
            <p class="habit-stat-card__label">Longest streak</p>
        </div>
        <div class="habit-stat-card" style="--stat-accent: {{ $color }}">
            <div class="habit-stat-card__icon">
                <x-icon name="check-circle" class="w-5 h-5" />
            </div>
            <p class="habit-stat-card__value">{{ $consistency }}%</p>
            <p class="habit-stat-card__label">This month</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-5 gap-6">
        {{-- Activity heatmap --}}
        <div class="lg:col-span-3 card habit-show-activity fade-in">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-label">Activity</h2>
                <span class="text-[10px] text-[#A8958B]">Last 12 weeks</span>
            </div>
            <x-habit-activity-grid :grid="$activityGrid" :color="$color" large />
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-[#EDE5DA]">
                <span class="text-[10px] text-[#A8958B]">Less</span>
                <div class="flex gap-1">
                    @foreach([0.12, 0.35, 0.55, 0.75, 1] as $opacity)
                        <span class="w-3 h-3 rounded-sm" style="background: {{ $color }}; opacity: {{ $opacity }}"></span>
                    @endforeach
                </div>
                <span class="text-[10px] text-[#A8958B]">More</span>
            </div>
        </div>

        {{-- Recent logs --}}
        <div class="lg:col-span-2 card fade-in">
            <h2 class="section-label mb-4">History</h2>
            @forelse($habit->logs as $log)
                <div class="habit-log-row">
                    <div class="habit-log-row__dot {{ $log->completed ? 'habit-log-row__dot--done' : '' }}"
                         style="{{ $log->completed ? '--dot-color: '.$color : '' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#3D3229]">
                            <x-jalali-date :date="$log->date" />
                            <span class="text-[#A8958B] font-normal"> · {{ $log->date->format('M j') }}</span>
                        </p>
                        @if($habit->type === 'timer' && $log->spent_minutes)
                            <p class="text-xs text-[#A8958B] mt-0.5">{{ $log->spent_minutes }} min logged</p>
                        @elseif($habit->type === 'counter')
                            <p class="text-xs text-[#A8958B] mt-0.5">{{ $log->count ?? 0 }} / {{ $habit->target_count }}</p>
                        @endif
                    </div>
                    <span class="habit-log-row__badge {{ $log->completed ? 'habit-log-row__badge--done' : 'habit-log-row__badge--missed' }}">
                        {{ $log->completed ? 'Done' : 'Missed' }}
                    </span>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="w-12 h-12 rounded-2xl bg-[#F3EDE4] flex items-center justify-center mx-auto mb-3">
                        <x-icon name="leaf" class="w-6 h-6 text-[#A8958B]" />
                    </div>
                    <p class="text-sm text-[#A8958B]">No logs yet — track this habit on Today.</p>
                    <a href="{{ route('planner.today') }}" class="inline-block mt-3 text-sm text-[#C47D5A] hover:underline">Go to Today →</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
