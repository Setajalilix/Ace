@props(['goal' => null, 'compact' => true])

@php $lifeAreas = auth()->user()->lifeAreas; @endphp

<div class="{{ $compact ? 'space-y-3' : 'space-y-4' }}">
    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $goal?->title) }}" class="input" required>
    </div>
    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Why (motivation)</label>
        <textarea name="why" class="input min-h-20">{{ old('why', $goal?->why) }}</textarea>
    </div>
    <div>
        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Success criteria</label>
        <textarea name="success_criteria" class="input min-h-20">{{ old('success_criteria', $goal?->success_criteria) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Type</label>
            <select name="type" class="input text-sm">
                <option value="annual" @selected(old('type', $goal?->type?->value ?? 'annual') === 'annual')>Annual</option>
                <option value="quarterly" @selected(old('type', $goal?->type?->value) === 'quarterly')>Quarterly</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Progress %</label>
            <input type="number" name="progress" value="{{ old('progress', $goal?->progress ?? 0) }}" min="0" max="100" class="input text-sm">
        </div>
    </div>
    <div>
        <x-date-input name="target_date" label="Target date" :value="old('target_date', $goal?->target_date?->toDateString())" :compact="$compact" />
    </div>
    @if($lifeAreas->isNotEmpty())
        <x-chip-select name="life_area_id" label="Life area" nullable collapsibleOnMobile
            :value="old('life_area_id', $goal?->life_area_id)"
            :options="$lifeAreas->map(fn ($a) => [
                'value' => $a->id,
                'label' => $a->name,
                'dot' => $a->color,
            ])->values()->all()" />
    @endif
</div>
