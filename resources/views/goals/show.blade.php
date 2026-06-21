@extends('layouts.app')
@section('title', $goal->title)
@section('content')
<div class="mb-6">
    <a href="{{ route('goals.index') }}" class="text-sm text-[#A8958B] hover:text-[#C47D5A]">&larr; Goals</a>
    <h1 class="page-title mt-2">{{ $goal->title }}</h1>
    @if($goal->lifeArea)<x-life-area-badge :area="$goal->lifeArea" class="mt-2" />@endif
</div>
<div class="grid lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
        <div class="h-2 bg-[#F3EDE4] rounded-full overflow-hidden">
            <div class="h-full bg-[#C47D5A] rounded-full transition-all duration-500" style="width: {{ $goal->progress }}%"></div>
        </div>
        <p class="text-sm text-[#A8958B]">{{ $goal->progress }}% complete · {{ $goal->type->label() }}</p>
        @if($goal->why)<div><p class="section-label mb-1">Why</p><p class="text-sm">{{ $goal->why }}</p></div>@endif
        <a href="{{ route('goals.edit', $goal) }}" class="btn-secondary inline-flex">Edit goal</a>
    </div>
    <div class="card">
        <h3 class="section-label mb-3">Linked Tasks</h3>
        @forelse($goal->tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-sm text-[#A8958B]">No tasks linked yet.</p>
        @endforelse
    </div>
</div>
@endsection
