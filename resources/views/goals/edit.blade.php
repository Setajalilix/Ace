@extends('layouts.app')

@section('title', 'Edit Goal — LifeOS')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Goal</h1>
<div class="card">
    <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $goal->title }}" class="input" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Why</label>
            <textarea name="why" class="input min-h-20">{{ $goal->why }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Success criteria</label>
            <textarea name="success_criteria" class="input min-h-20">{{ $goal->success_criteria }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" class="input">
                    <option value="annual" @selected($goal->type->value === 'annual')>Annual</option>
                    <option value="quarterly" @selected($goal->type->value === 'quarterly')>Quarterly</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Progress %</label>
                <input type="number" name="progress" value="{{ $goal->progress }}" min="0" max="100" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Target date</label>
            <input type="date" name="target_date" value="{{ $goal->target_date?->toDateString() }}" class="input">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Life area</label>
            <select name="life_area_id" class="input">
                <option value="">None</option>
                @foreach($lifeAreas as $area)
                    <option value="{{ $area->id }}" @selected($goal->life_area_id == $area->id)>{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('goals.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
