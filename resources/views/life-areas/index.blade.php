@extends('layouts.app')
@section('title', 'Life Areas — LifeOS')
@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="page-title">Life Areas</h1>
        <p class="text-sm text-[#A8958B] mt-1">Organize your life the way that fits you</p>
    </div>
    <a href="{{ route('life-areas.create') }}" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> Add Area</a>
</div>
<div class="grid sm:grid-cols-2 gap-4">
    @forelse($lifeAreas as $area)
        <div class="card group">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $area->color }}20">
                        <span class="w-4 h-4 rounded-full" style="background-color: {{ $area->color }}"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#3D3229]">{{ $area->name }}</h3>
                        <p class="text-xs text-[#A8958B]">{{ $area->tasks_count ?? $area->tasks()->count() }} tasks · {{ $area->habits()->count() }} habits</p>
                    </div>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('life-areas.edit', $area) }}" class="btn-ghost text-xs px-2 py-1">Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div class="card col-span-full text-center py-12 text-[#A8958B]">No life areas yet.</div>
    @endforelse
</div>
@endsection
