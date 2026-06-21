@extends('layouts.app')
@section('title', 'Edit Life Area — LifeOS')
@section('content')
<h1 class="page-title mb-6">Edit Life Area</h1>
<div class="card">
    <form method="POST" action="{{ route('life-areas.update', $lifeArea) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Name</label>
            <input type="text" name="name" value="{{ $lifeArea->name }}" class="input" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Color</label>
            <input type="color" name="color" value="{{ $lifeArea->color }}" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('life-areas.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
    @if(auth()->user()->lifeAreas()->count() > 1)
        <form method="POST" action="{{ route('life-areas.destroy', $lifeArea) }}" class="mt-6 pt-6 border-t border-[#EDE5DA]">
            @csrf @method('DELETE')
            <button class="text-sm text-[#E05D44] hover:underline">Delete this area</button>
        </form>
    @endif
</div>
@endsection
