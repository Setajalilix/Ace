@extends('layouts.app')

@section('title', 'New Goal — LifeOS')

@section('content')
<h1 class="text-2xl font-semibold mb-6">New Goal</h1>
<div class="card">
    <form method="POST" action="{{ route('goals.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" class="input" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Why (motivation)</label>
            <textarea name="why" class="input min-h-20"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Success criteria</label>
            <textarea name="success_criteria" class="input min-h-20"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" class="input">
                    <option value="annual">Annual</option>
                    <option value="quarterly">Quarterly (90 days)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Target date</label>
                <input type="date" name="target_date" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Life area</label>
            <select name="life_area_id" class="input">
                <option value="">None</option>
                @foreach($lifeAreas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Create goal</button>
            <a href="{{ route('goals.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
