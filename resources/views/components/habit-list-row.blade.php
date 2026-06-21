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

<div class="card-flat flex items-center gap-3 sm:gap-4 p-4">
    @if($habit->type === 'checkbox')
        <div class="shrink-0" @click.stop>
            <x-habit-item :habit="$habit" :log="$log" compact checkboxOnly />
        </div>
    @endif

    <a href="{{ route('habits.show', $habit) }}" class="flex items-center gap-3 min-w-0 flex-1 group">
        @if($habit->type !== 'checkbox')
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $habit->color ?? '#7BAE7F' }}"></span>
        @endif
        <div class="min-w-0">
            <span class="font-medium text-[#3D3229] truncate block group-hover:text-[#C47D5A]">{{ $habit->title }}</span>
            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                <p class="text-xs text-[#A8958B]">{{ ucfirst($habit->type) }} · every {{ $habit->repeat_every }} day(s)</p>
                @if($habit->lifeArea)<x-life-area-badge :area="$habit->lifeArea" />@endif
            </div>
        </div>
    </a>

    @if($habit->type !== 'checkbox')
        <div class="shrink-0 flex items-center justify-end ml-auto" @click.stop>
            <x-habit-item :habit="$habit" :log="$log" compact hideTitle alignEnd />
        </div>
    @endif

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
