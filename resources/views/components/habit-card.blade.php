@props(['habit', 'log' => null])

@php
    $log = $log ?? $habit->logs->first();
    $habitData = [
        'id' => $habit->id,
        'title' => $habit->title,
        'type' => $habit->type,
        'color' => $habit->color ?? '#7BAE7F',
        'repeat_every' => $habit->repeat_every,
        'start_date' => $habit->start_date->toDateString(),
        'target_minutes' => $habit->target_minutes,
        'target_count' => $habit->target_count,
        'daily_increment' => $habit->daily_increment ?? 0,
        'life_area_id' => $habit->life_area_id,
    ];
@endphp

<div class="card hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-3">
        @if($habit->type === 'checkbox')
            <div class="shrink-0" @click.stop>
                <x-habit-item :habit="$habit" :log="$log" compact checkboxOnly />
            </div>
        @endif

        <a href="{{ route('habits.show', $habit) }}" class="flex-1 min-w-0 group">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-medium text-[#3D3229] truncate group-hover:text-[#C47D5A]">{{ $habit->title }}</span>
                @if($habit->lifeArea)
                    <x-life-area-badge :area="$habit->lifeArea" />
                @endif
            </div>
            <p class="text-xs text-[#A8958B] mt-0.5">{{ ucfirst($habit->type) }} · every {{ $habit->repeat_every }} day(s)</p>
        </a>

        @if($log?->completed)
            <span class="text-xs font-medium text-[#7BAE7F] bg-[#E8F5E9] px-2 py-0.5 rounded-full shrink-0">Done</span>
        @endif

        <button type="button" @click.stop="window.dispatchEvent(new CustomEvent('ace:open-habit-edit', { detail: @js($habitData) }))"
                class="btn-ghost p-1.5 shrink-0 text-[#A8958B] hover:text-[#C47D5A]" aria-label="Edit habit">
            <x-icon name="pencil" class="w-4 h-4" />
        </button>
        <button type="button" @click.stop="window.dispatchEvent(new CustomEvent('ace:delete-habit', { detail: { id: {{ $habit->id }} } }))"
                class="btn-ghost p-1.5 shrink-0 text-[#A8958B] hover:text-[#E05D44]" aria-label="Delete habit">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>

    @if($habit->type !== 'checkbox')
        <x-habit-item :habit="$habit" :log="$log" compact hideTitle alignEnd />
    @endif

    @if(!empty($habit->activityGrid))
        <div class="mt-3 pt-3 border-t border-[#EDE5DA]">
            <x-habit-activity-grid :grid="$habit->activityGrid" :color="$habit->color ?? '#7BAE7F'" />
        </div>
    @endif
</div>
