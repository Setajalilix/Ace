@extends('layouts.app')
@section('title', 'Tasks — '.config('app.name'))
@section('content')
@php
    $hasActiveFilters = request()->hasAny(['q', 'status', 'priority', 'life_area_id']);
@endphp
<div x-data="{ showFilters: {{ $hasActiveFilters ? 'true' : 'false' }} }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="page-title">Tasks</h1>
            <p class="text-sm text-[#A8958B] mt-1">{{ $tasks->total() }} {{ request('status') === 'completed' ? 'completed' : 'active' }}</p>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="showFilters = !showFilters" class="btn-secondary">
                <x-icon name="board" class="w-4 h-4" />
                <span x-text="showFilters ? 'Hide filters' : 'Filters'"></span>
            </button>
            <button type="button" @click="openTaskCreate()" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> New Task</button>
        </div>
    </div>

    <form method="GET" x-show="showFilters" x-cloak class="card-flat mb-6 space-y-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search tasks..." class="input w-full">
        <div class="flex flex-wrap gap-2">
            <span class="text-xs text-[#A8958B] w-full">Status</span>
            @foreach(['' => 'Active', 'completed' => 'Completed', 'in_progress' => 'In progress', 'pending' => 'Pending'] as $val => $lbl)
                <a href="{{ route('tasks.index', array_merge(request()->except('status'), $val ? ['status' => $val] : [])) }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-full border {{ request('status', '') === $val ? 'bg-[#C47D5A] text-white border-[#C47D5A]' : 'bg-white text-[#6B5B4F] border-[#E8DDD4]' }}">{{ $lbl }}</a>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="text-xs text-[#A8958B] w-full">Priority</span>
            @foreach(['' => 'All', '1' => 'P1', '2' => 'P2', '3' => 'P3'] as $val => $lbl)
                <a href="{{ route('tasks.index', array_merge(request()->except('priority'), $val ? ['priority' => $val] : [])) }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-full border {{ request('priority', '') == $val ? 'bg-[#3D3229] text-white border-[#3D3229]' : 'bg-white text-[#6B5B4F] border-[#E8DDD4]' }}">{{ $lbl }}</a>
            @endforeach
        </div>
        @if($lifeAreas->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <span class="text-xs text-[#A8958B] w-full">Life area</span>
                <a href="{{ route('tasks.index', request()->except('life_area_id')) }}" class="px-3 py-1.5 text-xs font-medium rounded-full border {{ !request('life_area_id') ? 'bg-[#3D3229] text-white border-[#3D3229]' : 'bg-white border-[#E8DDD4]' }}">All</a>
                @foreach($lifeAreas as $area)
                    <a href="{{ route('tasks.index', array_merge(request()->all(), ['life_area_id' => $area->id])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full border inline-flex items-center gap-1 {{ request('life_area_id') == $area->id ? 'bg-[#3D3229] text-white border-[#3D3229]' : 'bg-white border-[#E8DDD4]' }}">
                        <span class="w-2 h-2 rounded-full" style="background:{{ $area->color }}"></span>{{ $area->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </form>

    <div class="card-flat divide-y divide-[#EDE5DA]">
        @forelse($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-[#A8958B] text-center py-12">No tasks match your filters.</p>
        @endforelse
    </div>
</div>
<div class="mt-4">{{ $tasks->links() }}</div>
@endsection
