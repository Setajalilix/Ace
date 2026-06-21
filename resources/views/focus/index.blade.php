<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Focus — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="focus-page min-h-screen flex flex-col items-center justify-center p-4 sm:p-8 overflow-hidden" x-data="{ completing: false }">
    {{-- Ambient layers --}}
    <div class="focus-bg" aria-hidden="true">
        <div class="focus-aurora"></div>
        <div class="focus-orb focus-orb-1"></div>
        <div class="focus-orb focus-orb-2"></div>
        <div class="focus-orb focus-orb-3"></div>
        <div class="focus-orb focus-orb-4"></div>
        <div class="focus-orb focus-orb-5"></div>
        <div class="focus-grid"></div>
        @for($i = 0; $i < 24; $i++)
            <span class="focus-particle" style="--i: {{ $i }}; --x: {{ rand(2, 98) }}%; --y: {{ rand(5, 95) }}%; --d: {{ rand(12, 28) }}s; --delay: -{{ rand(0, 20) }}s;"></span>
        @endfor
    </div>

    <div class="relative z-10 w-full max-w-xl space-y-8 focus-enter">
        {{-- Header --}}
        <div class="text-center space-y-2 focus-enter focus-enter--1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/50 backdrop-blur border border-white/60 text-xs font-medium text-[#C47D5A] uppercase tracking-widest">
                <span class="w-1.5 h-1.5 rounded-full bg-[#C47D5A] focus-dot-pulse"></span>
                Focus mode
            </div>
            <p class="text-sm text-[#6B5B4F]/80">Deep work · No distractions</p>
        </div>

        {{-- Context cards --}}
        @if($currentBlock || $currentTask)
        <div class="space-y-3 focus-enter focus-enter--2">
            @if($currentBlock)
                <div class="focus-glass focus-glass--block">
                    <p class="text-[#A8958B] text-xs font-medium uppercase tracking-wide">{{ substr($currentBlock->start_time,0,5) }} – {{ substr($currentBlock->end_time,0,5) }}</p>
                    <h1 class="text-xl font-semibold mt-1 text-[#3D3229]">{{ $currentBlock->title }}</h1>
                    @if($currentBlock->objective)
                        <p class="text-[#6B5B4F] mt-2 text-sm leading-relaxed">{{ $currentBlock->objective }}</p>
                    @endif
                </div>
            @endif

            @if($currentTask)
                <div class="focus-glass">
                    <p class="text-xs text-[#A8958B] uppercase tracking-wide mb-1">Current task</p>
                    <p class="text-base font-medium text-[#3D3229]">{{ $currentTask->title }}</p>
                </div>
            @endif
        </div>
        @elseif(!$currentBlock)
            <p class="text-center text-[#A8958B] text-sm focus-enter focus-enter--2">Schedule a time block from a task to begin.</p>
        @endif

        {{-- Timer --}}
        <div x-data="focusTimer(0, 'ace_focus_timer')" class="focus-enter focus-enter--3">
            <div class="relative flex flex-col items-center">
                {{-- Glow behind ring --}}
                <div class="focus-timer-glow" :class="running && 'focus-timer-glow--active'"></div>

                <div class="relative w-56 h-56 sm:w-64 sm:h-64 flex items-center justify-center">
                    <svg class="absolute inset-0 -rotate-90 w-full h-full focus-timer-ring" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="44" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="2"/>
                        <circle cx="50" cy="50" r="44" fill="none" stroke="#C47D5A" stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="276.5"
                                :stroke-dashoffset="276.5 - (276.5 * ringPct / 100)"
                                class="transition-all duration-1000 ease-out focus-timer-progress"
                                :class="running && 'focus-timer-progress--running'"/>
                    </svg>
                    <div class="absolute inset-4 rounded-full border border-white/30 focus-pulse" :class="running && 'focus-pulse--fast'"></div>
                    <div class="absolute inset-8 rounded-full border border-[#C47D5A]/10 focus-pulse focus-pulse-delay" :class="running && 'focus-pulse--fast'"></div>
                    <p class="text-5xl sm:text-6xl font-light tabular-nums text-[#3D3229] relative z-10 tracking-tight focus-timer-digits" x-text="formatted">00:00</p>
                </div>

                <p class="mt-4 text-xs text-[#A8958B]" x-show="running" x-cloak x-transition>Breathe. Stay present.</p>
                <p class="mt-4 text-xs text-[#A8958B]" x-show="!running" x-cloak x-transition>Press start when you're ready</p>
            </div>

            <div class="flex justify-center gap-3 mt-8">
                <button @click="toggle()" class="focus-btn focus-btn--primary" x-text="running ? 'Pause' : 'Start'">Start</button>
                <button @click="reset()" class="focus-btn focus-btn--ghost">Reset</button>
            </div>
            <p class="text-center text-[10px] text-[#A8958B]/70 mt-3">Timer persists across refreshes</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap justify-center gap-3 pt-2 focus-enter focus-enter--4">
            @if($currentTask && !$currentTask->isCompleted())
                <button type="button" :disabled="completing"
                        @click="async () => { completing = true; try { await acePost('{{ route('tasks.complete', $currentTask) }}', {}); aceToast('Task completed!'); setTimeout(() => window.location.href='{{ route('planner.today') }}', 800); } catch(e) { aceToast(e.message,'error'); completing=false; } }"
                        class="focus-btn focus-btn--primary">Complete task</button>
            @endif
            @if($currentBlock && $currentBlock->status->value !== 'completed')
                <button type="button"
                        @click="async () => { try { await acePost('{{ route('time-blocks.complete', $currentBlock) }}', {}); aceToast('Block completed!'); } catch(e) { aceToast(e.message,'error'); } }"
                        class="focus-btn focus-btn--ghost">Complete block</button>
            @endif
            <a href="{{ route('planner.today') }}" class="focus-btn focus-btn--ghost">Exit focus</a>
        </div>
    </div>

    <div x-data="{ toasts: [] }" x-init="window.addEventListener('ace:toast', e => { const id=Date.now(); toasts.push({id,...e.detail}); setTimeout(()=>toasts=toasts.filter(t=>t.id!==id),3200) })" class="fixed top-4 inset-x-4 z-50 flex flex-col gap-2 items-center pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div class="toast-enter px-4 py-3 rounded-xl bg-white/90 backdrop-blur shadow-lg border border-[#EDE5DA] text-sm font-medium" x-text="t.message"></div>
        </template>
    </div>
</body>
</html>
