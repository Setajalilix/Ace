@extends('layouts.app')
@section('title', 'Habits — '.config('app.name'))
@section('content')
<div x-data="{ view: localStorage.getItem('ace_habits_view') || 'grid' }"
     x-init="$watch('view', v => localStorage.setItem('ace_habits_view', v))"
     @ace:open-habit-create.window="window.dispatchEvent(new CustomEvent('ace:open-habit-create'))">
    <div class="flex items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="page-title">Habits</h1>
            <p class="text-sm text-[#A8958B] mt-1">Track and complete habits here</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <div class="flex gap-1 p-1 bg-[#F3EDE4] rounded-xl">
                <button type="button" @click="view = 'grid'" :class="view === 'grid' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="p-2 rounded-lg transition-all" title="Grid view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                </button>
                <button type="button" @click="view = 'list'" :class="view === 'list' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="p-2 rounded-lg transition-all" title="List view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75v-.008Zm0 5.25h.007v.008H3.75v-.008Z"/></svg>
                </button>
            </div>
            <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-habit-create'))" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> New Habit</button>
        </div>
    </div>

    <div x-show="view === 'grid'" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($habits as $habit)
            <x-habit-card :habit="$habit" :log="$habit->logs->first()" />
        @empty
            <div class="card col-span-full text-center py-12 text-[#A8958B]">No habits yet.</div>
        @endforelse
    </div>

    <div x-show="view === 'list'" x-cloak class="space-y-2">
        @forelse($habits as $habit)
            <x-habit-list-row :habit="$habit" :log="$habit->logs->first()" />
        @empty
            <div class="card text-center py-12 text-[#A8958B]">No habits yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $habits->links() }}</div>
</div>
@endsection
