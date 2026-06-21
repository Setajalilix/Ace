@props(['event' => null, 'compact' => true])

@php
    $lifeAreas = auth()->user()->lifeAreas;
    $startDate = $event?->starts_at?->toDateString() ?? today()->toDateString();
    $startTime = $event?->starts_at?->format('H:i') ?? now()->format('H:i');
    $endDate = $event?->ends_at?->toDateString() ?? $startDate;
    $endTime = $event?->ends_at?->format('H:i');
    $recurrence = 'none';
    if ($event?->recurrence_rule) {
        $rule = json_decode($event->recurrence_rule, true);
        $recurrence = ($rule['type'] ?? 'none') === 'custom' ? 'custom' : ($rule['type'] ?? 'none');
    }
@endphp

<div class="{{ $compact ? 'space-y-3' : 'space-y-4' }}"
     x-data="{ recurrence: '{{ $recurrence }}', showCustom: {{ $recurrence === 'custom' ? 'true' : 'false' }} }"
     @ace:event-fill.window="if ($event.detail) { recurrence = $event.detail.recurrence ?? 'none'; showCustom = recurrence === 'custom'; }">
    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $event?->title) }}" class="input" required>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <x-datetime-jalali-input name="start_date" timeName="start_time" dateLabel="Start date" timeLabel="Start time"
            :dateValue="$startDate" :timeValue="$startTime" />
        <x-datetime-jalali-input name="end_date" timeName="end_time" dateLabel="End date (optional)" timeLabel="End time"
            :dateValue="$endDate" :timeValue="$endTime" />
    </div>

    <div class="space-y-2">
        <label class="block text-xs font-medium text-[#6B5B4F]">Repeat</label>
        <input type="hidden" name="recurrence" :value="recurrence">
        <div class="flex flex-wrap gap-2">
            @foreach(\App\Domains\Events\Enums\EventRecurrence::cases() as $r)
                <button type="button" @click="recurrence='{{ $r->value }}'; showCustom=false"
                        :class="recurrence==='{{ $r->value }}' ? 'bg-[#C47D5A] text-white border-[#C47D5A]' : 'bg-white text-[#6B5B4F] border-[#E8DDD4]'"
                        class="px-3 py-1.5 text-xs font-medium rounded-full border">{{ $r->label() }}</button>
            @endforeach
            <button type="button" @click="recurrence='custom'; showCustom=true"
                    :class="recurrence==='custom' ? 'bg-[#C47D5A] text-white border-[#C47D5A]' : 'bg-white text-[#6B5B4F] border-[#E8DDD4]'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border">Custom…</button>
        </div>
    </div>

    @if($lifeAreas->isNotEmpty())
        <x-chip-select name="life_area_id" label="Life area" nullable collapsibleOnMobile
            :value="old('life_area_id', $event?->life_area_id)"
            :options="$lifeAreas->map(fn ($a) => [
                'value' => $a->id,
                'label' => $a->name,
                'dot' => $a->color,
            ])->values()->all()" />
    @endif

    <div x-show="showCustom" x-cloak class="p-4 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[#A8958B]">Every</span>
            <input type="number" name="recurrence_interval" value="1" min="1" max="99" class="input w-16">
            <div class="flex gap-1">
                @foreach(['day' => 'day(s)', 'week' => 'week(s)', 'month' => 'month(s)'] as $val => $lbl)
                    <label class="px-2 py-1 text-xs rounded-full border border-[#E8DDD4] has-[:checked]:bg-[#F3EDE4]">
                        <input type="radio" name="recurrence_unit" value="{{ $val }}" class="sr-only" {{ $val === 'week' ? 'checked' : '' }}> {{ $lbl }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach([6 => 'Sat', 0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri'] as $i => $label)
                <label class="px-2 py-1 text-xs rounded-full border border-[#E8DDD4] has-[:checked]:bg-[#C47D5A] has-[:checked]:text-white has-[:checked]:border-[#C47D5A]">
                    <input type="checkbox" name="recurrence_days[]" value="{{ $i }}" class="sr-only"> {{ $label }}
                </label>
            @endforeach
        </div>
        <x-date-input name="recurrence_end_date" label="Ends on (optional)" />
    </div>

    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Location <span class="text-[#A8958B] font-normal">optional</span></label>
        <input type="text" name="location" value="{{ old('location', $event?->location) }}" class="input">
    </div>
</div>
