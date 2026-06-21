@props(['task', 'showActions' => true])

@php
    $defaultStart = $task->scheduled_time ? substr($task->scheduled_time, 0, 5) : '21:00';
    $defaultEnd = $task->block_end_time ? substr($task->block_end_time, 0, 5) : '22:00';
    $defaultDate = $task->due_date?->toDateString() ?? today()->toDateString();
    $taskData = [
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => $task->priority->value,
        'status' => $task->status->value,
        'life_area_id' => $task->life_area_id,
        'goal_id' => $task->goal_id,
        'due_date' => $task->due_date?->toDateString(),
        'estimated_minutes' => $task->estimated_minutes,
        'scheduled_time' => $task->scheduled_time ? substr($task->scheduled_time, 0, 5) : null,
        'completed' => $task->isCompleted(),
    ];
@endphp

<div x-data="taskRow(@js($taskData))"
     x-show="!removing"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-x-0 max-h-24"
     x-transition:leave-end="opacity-0 -translate-x-3 max-h-0 overflow-hidden"
     class="group flex items-center gap-2 sm:gap-3 py-2.5 px-2 -mx-2 rounded-xl hover:bg-[#FAF7F2] transition-all duration-150">
    <button type="button" @click.stop="complete()" :disabled="completing || completed"
            class="w-5 h-5 rounded-full border-2 flex-shrink-0 transition-all duration-200 flex items-center justify-center"
            :class="completed ? 'bg-[#7BAE7F] border-[#7BAE7F] check-pop' : (completing ? 'border-[#7BAE7F] bg-[#7BAE7F]/30 scale-110' : 'border-[#D4C4B5] hover:border-[#C47D5A] hover:bg-[#FEE8E4]')">
        <svg x-show="completed" x-cloak class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
    </button>

    <div class="flex-1 min-w-0 cursor-pointer" @click="openEdit()">
        <div class="flex items-center gap-2 flex-wrap">
            <p class="text-sm font-medium transition-all duration-300"
               :class="completed ? 'line-through text-[#A8958B]' : 'text-[#3D3229]'">{{ $task->title }}</p>
            @if(!$task->isCompleted())
                <x-status-badge :status="$task->status" class="hidden sm:inline-flex" />
            @endif
        </div>
        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
            @if($task->lifeArea)<x-life-area-badge :area="$task->lifeArea" />@endif
            @if($task->scheduled_time && $task->block_end_time)
                <span class="text-xs text-[#6B9BD1]">{{ substr($task->scheduled_time, 0, 5) }}–{{ substr($task->block_end_time, 0, 5) }}</span>
                @if($task->daily_time_block)<span class="text-[10px] text-[#A8958B]">daily</span>@endif
            @elseif($task->scheduled_time)
                <span class="text-xs text-[#6B9BD1]">{{ substr($task->scheduled_time, 0, 5) }}</span>
            @endif
        </div>
    </div>

    <x-priority-badge :priority="$task->priority" />

    @if($showActions && !$task->isCompleted())
        <div class="flex items-center gap-0.5" x-show="!completed" @click.stop>
            @if($task->status->value !== 'in_progress')
                <button type="button" @click="start()" class="btn-ghost p-1.5" title="Start"><x-icon name="play" class="w-3.5 h-3.5" /></button>
            @endif
            <button type="button" @click="showBlock = true" class="btn-ghost p-1.5" title="Set time block"><x-icon name="clock" class="w-3.5 h-3.5" /></button>
            <button type="button" @click="openEdit()" class="btn-ghost p-1.5" title="Edit"><x-icon name="board" class="w-3.5 h-3.5" /></button>
        </div>
    @endif

    <x-modal show="showBlock" wide>
        <h3 class="font-medium text-[#3D3229] mb-1">Schedule time block</h3>
        <p class="text-xs text-[#A8958B] mb-4">Set when this happens — separate from Focus timer.</p>
        <form method="POST" action="{{ route('tasks.time-block', $task) }}" class="space-y-4"
              @submit.prevent="async (e) => { try { const d = await acePost('{{ route('tasks.time-block', $task) }}', { form: e.target }); aceToast(d.message ?? 'Time block saved'); showBlock = false; } catch(err) { aceToast(err.message, 'error'); } }">
            @csrf
            <x-date-input name="block_date" label="Date" :value="$defaultDate" />
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm text-[#6B5B4F] mb-1 block">Start time</label>
                    <input type="time" name="start_time" value="{{ $defaultStart }}" class="input" required>
                </div>
                <div>
                    <label class="text-sm text-[#6B5B4F] mb-1 block">End time</label>
                    <input type="time" name="end_time" value="{{ $defaultEnd }}" class="input" required>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-[#6B5B4F] cursor-pointer p-3 bg-[#FAF7F2] rounded-xl border border-[#EDE5DA]">
                <input type="checkbox" name="repeat_daily" value="1" class="rounded border-[#D4C4B5] text-[#C47D5A]" {{ $task->daily_time_block ? 'checked' : '' }}>
                Repeat daily at this time
            </label>
            <div class="flex gap-2 justify-end pt-2">
                <button type="button" @click="showBlock = false" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save time block</button>
            </div>
        </form>
    </x-modal>
</div>
