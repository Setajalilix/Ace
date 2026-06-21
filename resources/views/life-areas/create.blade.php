@extends('layouts.app')
@section('title', 'Add Life Area — LifeOS')
@section('content')
<h1 class="page-title mb-6">Add Life Area</h1>
<div class="card">
    <form method="POST" action="{{ route('life-areas.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Name</label>
            <input type="text" name="name" class="input" placeholder="e.g. Creative, Finance..." required>
        </div>
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Color</label>
            <div class="flex gap-2 items-center">
                <input type="color" name="color" value="#C47D5A" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                <span class="text-xs text-[#A8958B]">Pick a color for this area</span>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Create</button>
            <a href="{{ route('life-areas.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
