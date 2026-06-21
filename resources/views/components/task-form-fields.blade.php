@props(['task' => null, 'dueDate' => null, 'compact' => false, 'showStatus' => false])

@php
    $user = auth()->user();
    $lifeAreas = $user->lifeAreas;
    $goals = $user->goals()->latest()->limit(12)->get();
    $dueDate = $dueDate ?? $task?->due_date?->toDateString() ?? today()->toDateString();
@endphp

<div class="{{ $compact ? 'space-y-2.5' : 'space-y-4' }}">
    <div>
        <input type="text" name="title" value="{{ old('title', $task?->title) }}" placeholder="What needs to be done?"
               class="input {{ $compact ? 'text-sm py-2' : 'text-base text-lg py-3' }}" required autofocus>
    </div>

    @if($showStatus)
        <input type="hidden" name="status" value="{{ old('status', $task?->status?->value ?? 'pending') }}" x-ref="statusField">
        <x-chip-select name="status" label="Status" :value="$task?->status?->value ?? 'pending'" :options="[
            ['value' => 'pending', 'label' => 'Pending', 'active' => 'bg-[#F5F0EB] text-[#8B7355] border-[#E8DDD4]'],
            ['value' => 'in_progress', 'label' => 'In progress', 'active' => 'bg-[#E8F0FE] text-[#1A56DB] border-[#BBDEFB]'],
            ['value' => 'completed', 'label' => 'Completed', 'active' => 'bg-[#E8F5E9] text-[#2E7D32] border-[#C8E6C9]'],
        ]" />
    @endif

    <x-chip-select name="priority" label="Priority" collapsibleOnMobile :value="old('priority', $task?->priority?->value ?? 2)" :options="[
        ['value' => '1', 'label' => '🔴 P1 Must', 'active' => 'bg-[#FEE8E4] text-[#C0392B] border-[#F5C4BC]'],
        ['value' => '2', 'label' => '🟡 P2 Should', 'active' => 'bg-[#FEF3E0] text-[#B7791F] border-[#F5DFB8]'],
        ['value' => '3', 'label' => '🟢 P3 Optional', 'active' => 'bg-[#F5F0EB] text-[#8B7355] border-[#E8DDD4]'],
    ]" />

    @if($lifeAreas->isNotEmpty())
        <x-chip-select name="life_area_id" label="Life area" nullable collapsibleOnMobile :value="old('life_area_id', $task?->life_area_id)" :options="$lifeAreas->map(fn ($a) => [
            'value' => $a->id,
            'label' => $a->name,
            'dot' => $a->color,
        ])->values()->all()" />
    @endif

    @if($goals->isNotEmpty())
        <x-chip-select name="goal_id" label="Goal" nullable collapsibleOnMobile :value="old('goal_id', $task?->goal_id)" :options="$goals->map(fn ($g) => [
            'value' => $g->id,
            'label' => $g->title,
        ])->values()->all()" />
    @endif

    <div class="{{ $compact ? 'space-y-3' : 'grid grid-cols-2 sm:grid-cols-3 gap-3' }}">
        <div class="{{ $compact ? '' : 'col-span-2 sm:col-span-1' }}">
            <x-date-input name="due_date" label="Due date" :value="$dueDate" :compact="$compact" />
        </div>
        @if(!$compact)
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Duration</label>
            <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $task?->estimated_minutes ?? 30) }}" min="5" step="5" class="input text-sm py-2" placeholder="30">
        </div>
        <div>
            <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Start <span class="text-[#A8958B] font-normal">opt.</span></label>
            <input type="time" name="scheduled_time" value="{{ old('scheduled_time', $task?->scheduled_time ? substr($task->scheduled_time, 0, 5) : '') }}" class="input text-sm py-2">
        </div>
        @else
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Duration</label>
                <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $task?->estimated_minutes ?? 30) }}" min="5" step="5" class="input text-sm py-2" placeholder="30">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Start <span class="text-[#A8958B] font-normal">opt.</span></label>
                <input type="time" name="scheduled_time" value="{{ old('scheduled_time', $task?->scheduled_time ? substr($task->scheduled_time, 0, 5) : '') }}" class="input text-sm py-2">
            </div>
        </div>
        @endif
    </div>
</div>
