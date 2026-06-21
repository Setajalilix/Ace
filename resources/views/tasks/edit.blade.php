@extends('layouts.app')
@section('title', 'Edit Task — '.config('app.name'))
@section('content')
<h1 class="page-title mb-6">Edit Task</h1>
<div class="card">
    <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $task->title) }}" class="input" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Description</label>
            <textarea name="description" class="input min-h-20">{{ old('description', $task->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Priority</label>
                <select name="priority" class="input">
                    @foreach([1,2,3] as $p)<option value="{{ $p }}" @selected($task->priority->value === $p)>P{{ $p }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Status</label>
                <select name="status" class="input">
                    @foreach(['pending','in_progress','completed','cancelled','delayed'] as $s)
                        <option value="{{ $s }}" @selected($task->status->value === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Life area</label>
                <select name="life_area_id" class="input">
                    <option value="">None</option>
                    @foreach($lifeAreas as $area)<option value="{{ $area->id }}" @selected($task->life_area_id == $area->id)>{{ $area->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Goal</label>
                <select name="goal_id" class="input">
                    <option value="">None</option>
                    @foreach($goals as $goal)<option value="{{ $goal->id }}" @selected($task->goal_id == $goal->id)>{{ $goal->title }}</option>@endforeach
                </select>
            </div>
        </div>
        <x-date-input name="due_date" label="Due date" :value="$task->due_date?->toDateString()" />
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Start time</label>
                <input type="time" name="scheduled_time" value="{{ old('scheduled_time', $task->scheduled_time ? substr($task->scheduled_time, 0, 5) : '') }}" class="input">
                <p class="text-[10px] text-[#A8958B] mt-1">Auto-starts on board at this time; rolls to tomorrow if missed.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Estimated minutes</label>
                <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="1" class="input">
            </div>
        </div>
        <div class="flex flex-wrap gap-2 pt-2 border-t border-[#EDE5DA]">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-[#EDE5DA]">
        @if(!$task->isCompleted())
            <form method="POST" action="{{ route('tasks.start', $task) }}">@csrf<button type="submit" class="btn-secondary">Start now</button></form>
            <form method="POST" action="{{ route('tasks.time-block', $task) }}">@csrf<button type="submit" class="btn-secondary">Schedule time block</button></form>
        @endif
        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="ml-auto" onsubmit="return confirm('Delete this task?')">@csrf @method('DELETE')
            <button type="submit" class="btn-ghost text-[#E05D44]">Delete task</button>
        </form>
    </div>
</div>
@endsection
