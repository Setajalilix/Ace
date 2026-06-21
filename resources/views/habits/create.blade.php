@extends('layouts.app')
@section('title', 'New Habit — '.config('app.name'))
@section('content')
<h1 class="page-title mb-6">New Habit</h1>
<div class="card">
    <form method="POST" action="{{ route('habits.store') }}" class="space-y-4" x-data="{ type: 'checkbox' }">
        @csrf
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Title</label>
            <input type="text" name="title" class="input" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Type</label>
                <select name="type" class="input" x-model="type">
                    <option value="checkbox">Checkbox (done / not done)</option>
                    <option value="timer">Timer (minutes per day)</option>
                    <option value="counter">Counter (e.g. 5 glasses, 30 pages)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Color</label>
                <input type="color" name="color" value="#7BAE7F" class="input h-10">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Repeat every (days)</label>
                <input type="number" name="repeat_every" value="1" min="1" class="input">
            </div>
            <div>
                <x-date-input name="start_date" label="Start date" :value="today()->toDateString()" />
            </div>
        </div>
        <div x-show="type === 'timer'" x-cloak class="p-4 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Starting minutes per day</label>
                <input type="number" name="target_minutes" value="5" min="1" class="input" placeholder="e.g. 5">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Increase by (minutes/day)</label>
                <input type="number" name="daily_increment" value="0" min="0" class="input" placeholder="e.g. 5 for +5 min each day">
                <p class="text-[10px] text-[#A8958B] mt-1">Leave 0 for a fixed daily target.</p>
            </div>
        </div>
        <div x-show="type === 'counter'" x-cloak class="p-4 bg-[#FAF7F2] rounded-xl space-y-3 border border-[#EDE5DA]">
            <div>
                <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Daily target count</label>
                <input type="number" name="target_count" value="5" min="1" class="input" placeholder="e.g. 5 glasses of water">
                <p class="text-[10px] text-[#A8958B] mt-1">Use +1 on Today or Habits page to log progress.</p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Life area</label>
            <select name="life_area_id" class="input">
                <option value="">None</option>
                @foreach($lifeAreas as $area)<option value="{{ $area->id }}">{{ $area->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Create habit</button>
            <a href="{{ route('habits.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
