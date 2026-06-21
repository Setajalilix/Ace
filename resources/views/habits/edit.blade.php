@extends('layouts.app')
@section('title', 'Edit Habit — '.config('app.name'))
@section('content')
<h1 class="page-title mb-6">Edit Habit</h1>
<div class="card" x-data="{ confirmDelete: false }">
    <form method="POST" action="{{ route('habits.update', $habit) }}" class="space-y-4" x-data="{ type: '{{ $habit->type }}' }">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Title</label>
            <input type="text" name="title" value="{{ $habit->title }}" class="input" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Type</label>
                <select name="type" class="input" x-model="type">
                    <option value="checkbox" @selected($habit->type === 'checkbox')>Checkbox (done / not done)</option>
                    <option value="timer" @selected($habit->type === 'timer')>Timer (minutes per day)</option>
                    <option value="counter" @selected($habit->type === 'counter')>Counter (e.g. 5 glasses, 30 pages)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Color</label>
                <input type="color" name="color" value="{{ $habit->color ?? '#7BAE7F' }}" class="input h-10">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Repeat every (days)</label>
                <input type="number" name="repeat_every" value="{{ $habit->repeat_every }}" min="1" class="input">
            </div>
            <div>
                <x-date-input name="start_date" label="Start date" :value="$habit->start_date->toDateString()" />
            </div>
        </div>
        <div x-show="type === 'timer'" x-cloak class="p-4 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Starting minutes per day</label>
                <input type="number" name="target_minutes" value="{{ $habit->target_minutes }}" min="1" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Increase by (minutes/day)</label>
                <input type="number" name="daily_increment" value="{{ $habit->daily_increment ?? 0 }}" min="0" class="input">
            </div>
        </div>
        <div x-show="type === 'counter'" x-cloak class="p-4 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Daily target count</label>
                <input type="number" name="target_count" value="{{ $habit->target_count ?? 5 }}" min="1" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Life area</label>
            <select name="life_area_id" class="input">
                <option value="">None</option>
                @foreach($lifeAreas as $area)<option value="{{ $area->id }}" @selected($habit->life_area_id == $area->id)>{{ $area->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex gap-2 pt-2 border-t border-[#EDE5DA]">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('habits.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>

    <div class="mt-4 pt-4 border-t border-[#EDE5DA]">
        <template x-if="!confirmDelete">
            <button type="button" @click="confirmDelete = true" class="btn-ghost text-[#E05D44] w-full sm:w-auto">Delete habit</button>
        </template>
        <div x-show="confirmDelete" x-cloak class="p-4 bg-[#FEE8E4] border border-[#F5C4BC] rounded-xl space-y-3">
            <p class="text-sm text-[#C0392B]">Delete <strong>{{ $habit->title }}</strong>? This removes all logs and cannot be undone.</p>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('habits.destroy', $habit) }}">@csrf @method('DELETE')
                    <button type="submit" class="btn-primary bg-[#E05D44] hover:bg-[#C0392B] border-none shadow-none">Yes, delete</button>
                </form>
                <button type="button" @click="confirmDelete = false" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
