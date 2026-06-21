@props(['habit' => null, 'compact' => true])

@php
    $lifeAreas = auth()->user()->lifeAreas;
    $startDate = $habit?->start_date?->toDateString() ?? today()->toDateString();
@endphp

<div class="{{ $compact ? 'space-y-3' : 'space-y-4' }}" x-data="{ type: '{{ old('type', $habit?->type ?? 'checkbox') }}' }"
     @ace:habit-fill.window="if ($event.detail) type = $event.detail.type || 'checkbox'">
    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $habit?->title) }}" class="input" required>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Type</label>
            <select name="type" class="input text-sm" x-model="type">
                <option value="checkbox">Checkbox</option>
                <option value="timer">Timer</option>
                <option value="counter">Counter</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Color</label>
            <input type="color" name="color" value="{{ old('color', $habit?->color ?? '#7BAE7F') }}" class="w-full h-10 rounded-xl border-0 cursor-pointer">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Repeat every (days)</label>
            <input type="number" name="repeat_every" value="{{ old('repeat_every', $habit?->repeat_every ?? 1) }}" min="1" class="input text-sm">
        </div>
        <div>
            <x-date-input name="start_date" label="Start date" :value="$startDate" compact />
        </div>
    </div>

    <div x-show="type === 'timer'" x-cloak class="p-3 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Minutes per day</label>
            <input type="number" name="target_minutes" value="{{ old('target_minutes', $habit?->target_minutes ?? 5) }}" min="1" class="input text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Daily increase (min)</label>
            <input type="number" name="daily_increment" value="{{ old('daily_increment', $habit?->daily_increment ?? 0) }}" min="0" class="input text-sm">
        </div>
    </div>

    <div x-show="type === 'counter'" x-cloak class="p-3 bg-[#FAF7F2] rounded-xl border border-[#EDE5DA]">
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Daily target count</label>
        <input type="number" name="target_count" value="{{ old('target_count', $habit?->target_count ?? 5) }}" min="1" class="input text-sm">
    </div>

    @if($lifeAreas->isNotEmpty())
        <x-chip-select name="life_area_id" label="Life area" nullable collapsibleOnMobile
            :value="old('life_area_id', $habit?->life_area_id)"
            :options="$lifeAreas->map(fn ($a) => [
                'value' => $a->id,
                'label' => $a->name,
                'dot' => $a->color,
            ])->values()->all()" />
    @endif
</div>
