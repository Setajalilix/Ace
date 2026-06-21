<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@auth
@php
    $wide = request()->routeIs('board.*', 'tasks.*', 'planner.*', 'calendar.*', 'settings.*', 'habits.*', 'goals.*', 'events.*');
    $nav = [
        ['route' => 'planner.today', 'label' => 'Today', 'icon' => 'sun', 'match' => 'planner.*'],
        ['route' => 'inbox.index', 'label' => 'Inbox', 'icon' => 'inbox', 'match' => 'inbox.*', 'badge' => auth()->user()->inboxItems()->unprocessed()->count()],
        ['route' => 'tasks.index', 'label' => 'Tasks', 'icon' => 'check-circle', 'match' => 'tasks.*'],
        ['route' => 'board.index', 'label' => 'Board', 'icon' => 'board', 'match' => 'board.*'],
        ['route' => 'habits.index', 'label' => 'Habits', 'icon' => 'leaf', 'match' => 'habits.*'],
        ['route' => 'calendar.index', 'label' => 'Calendar', 'icon' => 'calendar', 'match' => 'calendar.*'],
    ];
@endphp
<div class="flex min-h-screen pb-16 lg:pb-0" x-data="appShell">
    <aside class="hidden lg:flex w-60 flex-col min-h-screen sticky top-0 self-start bg-white border-r border-[#EDE5DA] p-4 shrink-0">
        <a href="{{ route('planner.today') }}" class="flex items-center gap-2 px-2 mb-6">
            <div class="w-8 h-8 rounded-xl bg-[#C47D5A] flex items-center justify-center">
                <span class="text-white font-bold text-sm">A</span>
            </div>
            <span class="font-semibold text-[#3D3229]">{{ config('app.name') }}</span>
        </a>
        <nav class="flex-1 space-y-1">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['match']) ? 'nav-link-active' : 'nav-link' }}">
                    <x-icon :name="$item['icon']" class="w-4 h-4" />
                    {{ $item['label'] }}
                    @if(!empty($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto text-xs bg-[#C47D5A] text-white px-1.5 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('goals.index') }}" class="{{ request()->routeIs('goals.*') ? 'nav-link-active' : 'nav-link' }}"><x-icon name="target" class="w-4 h-4" /> Goals</a>
            <a href="{{ route('journal.index') }}" class="{{ request()->routeIs('journal.*') ? 'nav-link-active' : 'nav-link' }}"><x-icon name="leaf" class="w-4 h-4" /> Journal</a>
            <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'nav-link-active' : 'nav-link' }}"><x-icon name="calendar" class="w-4 h-4" /> Events</a>
            <a href="{{ route('statistics.index') }}" class="{{ request()->routeIs('statistics.*') ? 'nav-link-active' : 'nav-link' }}"><x-icon name="bolt" class="w-4 h-4" /> Stats</a>
        </nav>
        <div class="mt-auto pt-4 border-t border-[#EDE5DA] space-y-1">
            <a href="{{ route('focus.index') }}" class="nav-link"><x-icon name="play" class="w-4 h-4" /> Focus</a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'nav-link-active' : 'nav-link' }}"><x-icon name="cog" class="w-4 h-4" /> Settings</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="nav-link w-full text-left">Log out</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto min-w-0">
        <div class="{{ $wide ? 'max-w-7xl' : 'max-w-5xl' }} mx-auto px-4 py-6 lg:py-8 lg:px-8 fade-in">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 text-sm bg-[#E8F5E9] text-[#2E7D32] rounded-xl border border-[#C8E6C9]">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-3 text-sm bg-[#FEE8E4] text-[#C0392B] rounded-xl border border-[#F5C4BC]">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    {{-- Mobile bottom nav --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-[#EDE5DA] px-2 py-1.5 flex justify-around">
        @foreach(array_slice($nav, 0, 5) as $item)
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] {{ request()->routeIs($item['match']) ? 'text-[#C47D5A] font-medium' : 'text-[#A8958B]' }}">
                <x-icon :name="$item['icon']" class="w-5 h-5" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- FAB menu --}}
    <div class="fixed bottom-20 lg:bottom-8 right-4 z-50 flex flex-col items-end gap-2">
        <div x-show="fabMenu" x-cloak x-transition class="flex flex-col gap-2 mb-1">
            <button @click="openTaskCreate(); fabMenu = false" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-[#EDE5DA] shadow-md text-sm font-medium text-[#3D3229] hover:bg-[#FAF7F2]">
                <x-icon name="check-circle" class="w-4 h-4 text-[#C47D5A]" /> Add task
            </button>
            <button @click="openCapture(); fabMenu = false" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-[#EDE5DA] shadow-md text-sm font-medium text-[#3D3229] hover:bg-[#FAF7F2]">
                <x-icon name="inbox" class="w-4 h-4 text-[#C47D5A]" /> Quick capture
            </button>
        </div>
        <button @click="fabMenu = !fabMenu"
                class="w-14 h-14 rounded-full bg-[#C47D5A] text-white shadow-lg shadow-[#C47D5A]/30 flex items-center justify-center hover:bg-[#A86545] active:scale-95 transition-all"
                :class="fabMenu && 'rotate-45'"
                aria-label="Add">
            <x-icon name="plus" class="w-6 h-6" />
        </button>
    </div>

    {{-- Quick capture modal --}}
    <x-modal show="open" close="requestCloseCapture()">
        <h3 class="font-medium text-[#3D3229] mb-1">Quick Capture</h3>
        <p class="text-xs text-[#A8958B] mb-3">Brain dump — process later in Inbox</p>
        <textarea x-model="body" @keydown.meta.enter="submitCapture()" @keydown.ctrl.enter="submitCapture()" class="input min-h-24" placeholder="What's on your mind?" autofocus></textarea>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button" @click="requestCloseCapture()" class="btn-secondary">Cancel</button>
            <button @click="submitCapture()" class="btn-primary">Capture</button>
        </div>
    </x-modal>

    {{-- Create / edit task modal --}}
    <x-modal show="taskOpen" wide compact close="requestCloseTask()">
        <h3 class="font-semibold text-base text-[#3D3229] mb-0.5" x-text="taskEditId ? 'Edit task' : 'New task'">New task</h3>
        <p class="text-xs text-[#A8958B] mb-4" x-text="taskEditId ? 'Update details' : 'Add to your day'"></p>
        <form x-ref="taskForm" @submit.prevent="submitTask($event)" class="space-y-4">
            @csrf
            <x-task-form-fields compact :dueDate="today()->toDateString()" />
            <div x-show="taskEditId" x-cloak class="pt-1">
                <x-chip-select name="status" label="Status" collapsibleOnMobile value="pending" :options="[
                    ['value' => 'pending', 'label' => 'Pending', 'active' => 'bg-[#F5F0EB] text-[#8B7355] border-[#E8DDD4]'],
                    ['value' => 'in_progress', 'label' => 'In progress', 'active' => 'bg-[#E8F0FE] text-[#1A56DB] border-[#BBDEFB]'],
                    ['value' => 'completed', 'label' => 'Completed', 'active' => 'bg-[#E8F5E9] text-[#2E7D32] border-[#C8E6C9]'],
                    ['value' => 'cancelled', 'label' => 'Cancelled', 'active' => 'bg-stone-50 text-stone-500 border-stone-200'],
                    ['value' => 'delayed', 'label' => 'Suspended', 'active' => 'bg-amber-50 text-amber-700 border-amber-200'],
                ]" />
            </div>
            <div class="flex justify-end gap-2 pt-3 sticky bottom-0 bg-white pb-0.5 -mx-1 px-1">
                <button type="button" @click="requestCloseTask()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="taskSaving">
                    <span x-show="!taskSaving" x-text="taskEditId ? 'Save changes' : 'Create task'"></span>
                    <span x-show="taskSaving" x-cloak>Saving…</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Create / edit habit modal --}}
    <x-modal show="habitOpen" wide compact close="requestCloseHabit()">
        <h3 class="font-semibold text-base text-[#3D3229] mb-0.5" x-text="habitEditId ? 'Edit habit' : 'New habit'">New habit</h3>
        <p class="text-xs text-[#A8958B] mb-4" x-text="habitEditId ? 'Update your habit' : 'Build a new routine'"></p>
        <form x-ref="habitForm" @submit.prevent="submitHabit($event)" class="space-y-4">
            @csrf
            <x-habit-form-fields compact />
            <div class="flex items-center justify-between gap-2 pt-3 sticky bottom-0 bg-white pb-0.5 -mx-1 px-1">
                <button type="button" x-show="habitEditId" x-cloak @click="deleteHabit()"
                        class="btn-ghost text-sm text-[#E05D44] hover:bg-[#FEE8E4] px-3">
                    Delete
                </button>
                <div class="flex gap-2 ml-auto">
                <button type="button" @click="requestCloseHabit()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="habitSaving">
                    <span x-show="!habitSaving" x-text="habitEditId ? 'Save changes' : 'Create habit'"></span>
                    <span x-show="habitSaving" x-cloak>Saving…</span>
                </button>
                </div>
            </div>
        </form>
    </x-modal>

    {{-- Create / edit goal modal --}}
    <x-modal show="goalOpen" wide compact close="requestCloseGoal()">
        <h3 class="font-semibold text-base text-[#3D3229] mb-0.5" x-text="goalEditId ? 'Edit goal' : 'New goal'">New goal</h3>
        <p class="text-xs text-[#A8958B] mb-4" x-text="goalEditId ? 'Update your goal' : 'Set a meaningful target'"></p>
        <form x-ref="goalForm" @submit.prevent="submitGoal($event)" class="space-y-4">
            @csrf
            <x-goal-form-fields compact />
            <div class="flex justify-end gap-2 pt-3 sticky bottom-0 bg-white pb-0.5 -mx-1 px-1">
                <button type="button" @click="requestCloseGoal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="goalSaving">
                    <span x-show="!goalSaving" x-text="goalEditId ? 'Save changes' : 'Create goal'"></span>
                    <span x-show="goalSaving" x-cloak>Saving…</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Create / edit event modal --}}
    <x-modal show="eventOpen" wide compact close="requestCloseEvent()">
        <h3 class="font-semibold text-base text-[#3D3229] mb-0.5" x-text="eventEditId ? 'Edit event' : 'New event'">New event</h3>
        <p class="text-xs text-[#A8958B] mb-4" x-text="eventEditId ? 'Update details' : 'Schedule something'"></p>
        <form x-ref="eventForm" @submit.prevent="submitEvent($event)" class="space-y-4">
            @csrf
            <x-event-form-fields compact />
            <div x-show="eventEditId" x-cloak class="pt-1">
                <x-chip-select name="status" label="Status" collapsibleOnMobile value="scheduled" :options="[
                    ['value' => 'scheduled', 'label' => 'Scheduled', 'active' => 'bg-sky-50 text-sky-700 border-sky-200'],
                    ['value' => 'completed', 'label' => 'Completed', 'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    ['value' => 'missed', 'label' => 'Missed', 'active' => 'bg-red-50 text-red-700 border-red-200'],
                    ['value' => 'cancelled', 'label' => 'Cancelled', 'active' => 'bg-stone-50 text-stone-500 border-stone-200'],
                    ['value' => 'delayed', 'label' => 'Delayed', 'active' => 'bg-amber-50 text-amber-700 border-amber-200'],
                ]" />
            </div>
            <div class="flex justify-end gap-2 pt-3 sticky bottom-0 bg-white pb-0.5 -mx-1 px-1">
                <button type="button" @click="requestCloseEvent()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="eventSaving">
                    <span x-show="!eventSaving" x-text="eventEditId ? 'Save changes' : 'Create event'"></span>
                    <span x-show="eventSaving" x-cloak>Saving…</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Toast notifications --}}
    <div class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-6 sm:left-auto z-[70] flex flex-col gap-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast-enter pointer-events-auto flex items-center gap-2 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium max-w-sm ml-auto"
                 :class="toast.type === 'error' ? 'bg-[#FEE8E4] text-[#C0392B] border-[#F5C4BC]' : 'bg-white text-[#3D3229] border-[#EDE5DA]'">
                <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                      :class="toast.type === 'error' ? 'bg-[#F5C4BC]' : 'bg-[#E8F5E9]'">
                    <svg x-show="toast.type !== 'error'" class="w-3 h-3 text-[#2E7D32]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="toast.type === 'error'" class="w-3 h-3 text-[#C0392B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>
</div>
@else
    @yield('content')
@endauth
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
