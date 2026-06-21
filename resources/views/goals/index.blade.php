@extends('layouts.app')
@section('title', 'Goals — '.config('app.name'))
@section('content')
<div class="flex items-center justify-between mb-8">
    <h1 class="page-title">Goals</h1>
    <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-goal-create'))" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> New Goal</button>
</div>
<div class="grid gap-4">
    @forelse($goals as $goal)
        @php
            $goalData = [
                'id' => $goal->id,
                'title' => $goal->title,
                'why' => $goal->why,
                'success_criteria' => $goal->success_criteria,
                'type' => $goal->type->value,
                'target_date' => $goal->target_date?->toDateString(),
                'progress' => $goal->progress,
                'life_area_id' => $goal->life_area_id,
            ];
        @endphp
        <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-goal-edit', { detail: @js($goalData) }))"
                class="card block hover:border-[#C47D5A]/30 text-left w-full transition-all">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-[#3D3229]">{{ $goal->title }}</h3>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="text-xs text-[#A8958B]">{{ $goal->type->label() }}</span>
                        @if($goal->lifeArea)<x-life-area-badge :area="$goal->lifeArea" />@endif
                    </div>
                </div>
                <span class="text-sm font-semibold text-[#C47D5A]">{{ $goal->progress }}%</span>
            </div>
            <div class="h-1.5 bg-[#F3EDE4] rounded-full overflow-hidden">
                <div class="h-full bg-[#C47D5A] rounded-full transition-all" style="width: {{ $goal->progress }}%"></div>
            </div>
        </button>
    @empty
        <div class="card text-center py-12 text-[#A8958B]">Set your first SMART goal.</div>
    @endforelse
</div>
@endsection
