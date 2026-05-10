@extends('layouts.app')

@section('content')

    <div class="space-y-8">

        {{-- Hero Section --}}
        <section class="glass-card p-8 lg:p-10 relative overflow-hidden">

            <div class="absolute -top-24 left-0 w-80 h-80 bg-indigo-500/10 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 -right-24 w-80 h-80 bg-fuchsia-500/10 blur-3xl rounded-full"></div>

            <div class="relative z-10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-10">

                <div class="max-w-2xl">

                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-zinc-300 mb-6">

                        <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>

                        امروز فرصت ساختن نسخه بهتر خودته

                    </div>

                    <h1 class="text-4xl lg:text-6xl font-black leading-tight">

                        سلام،
                        <span class="bg-gradient-to-r from-indigo-400 to-fuchsia-400 bg-clip-text text-transparent">
                        Setayesh
                    </span>

                    </h1>

                    <p class="mt-6 text-zinc-400 text-lg leading-9">

                        توی این مسیر صعب العبور این برگ برنده من و تو بود

                    </p>

                </div>

                <div class="grid grid-cols-2 gap-4 min-w-[320px]">

                    <div class="glass-card p-5">
                        <div class="text-zinc-500 text-sm mb-2">
                            عادت‌های امروز
                        </div>

                        <div class="text-4xl font-black">
                            {{ count($habits) }}
                        </div>
                    </div>

                    <div class="glass-card p-5">
                        <div class="text-zinc-500 text-sm mb-2">
                            نرخ تکمیل
                        </div>

                        <div class="text-4xl font-black text-emerald-400">
                            {{ $completionRate ?? 0 }}%
                        </div>
                    </div>

                    <div class="glass-card p-5">
                        <div class="text-zinc-500 text-sm mb-2">
                            استریک فعلی
                        </div>

                        <div class="text-4xl font-black text-orange-400">
                            {{ $streak ?? 0 }}
                        </div>
                    </div>

                    <div class="glass-card p-5">
                        <div class="text-zinc-500 text-sm mb-2">
                            زمان تمرکز
                        </div>

                        <div class="text-4xl font-black text-sky-400">
                            {{ $focusMinutes ?? 0 }}m
                        </div>
                    </div>

                </div>

            </div>

        </section>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

            <div>

                <h2 class="text-3xl font-black">
                    عادت‌های امروز
                </h2>

                <p class="text-zinc-500 mt-3 leading-8">

                    از تعطیلات لذت ببر

                </p>

            </div>

            <a href="{{ route('habits.create') }}"
               class="h-14 px-7 rounded-2xl bg-white text-black flex items-center justify-center font-bold hover:scale-[1.02] transition">

                + افزودن عادت

            </a>

        </div>

        {{-- Habits Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">

            @foreach($habits as $habit)

                @php

                    $log = $habit->logs->firstWhere('date', today());

                    $completed = $log?->completed;

                    $spent = $log?->spent_minutes ?? 0;

                    $target = $habit->todayTargetMinutes();

                    $progress = $target > 0
                        ? min(($spent / $target) * 100, 100)
                        : 0;

                @endphp

                <div class="habit-card p-6 relative">

                    {{-- Top Border --}}
                    <div class="absolute top-0 left-0 w-full h-1"
                         style="background: {{ $habit->color }}">
                    </div>

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-8">

                        <div class="flex items-center gap-4">

                            <div
                                class="w-16 h-16 rounded-3xl flex items-center justify-center text-3xl"
                                style="
                                background: {{ $habit->color }}20;
                                color: {{ $habit->color }};
                            "
                            >
                                {{ $habit->icon }}
                            </div>

                            <div>

                                <h3 class="text-xl font-bold">
                                    {{ $habit->title }}
                                </h3>

                                <div class="flex items-center gap-2 text-sm text-zinc-500 mt-2">

                                <span>
                                    هر {{ $habit->repeat_every }} روز
                                </span>

                                    @if($habit->has_time_block)

                                        <span class="w-1 h-1 rounded-full bg-zinc-600"></span>

                                        <span>
                                        {{ $habit->block_time }}
                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                        @if($completed)

                            <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                                ✓
                            </div>

                        @endif

                    </div>

                    {{-- Checkbox Habit --}}
                    @if($habit->type === 'checkbox')

                        <div class="space-y-5">

                            <div class="progress-track">

                                <div class="progress-fill bg-emerald-400"
                                     style="width: {{ $completed ? 100 : 12 }}%">
                                </div>

                            </div>

                            <form method="POST"
                                  action="{{ route('habits.toggle', $habit) }}">

                                @csrf

                                <button
                                    class="
                                    w-full py-4 rounded-2xl font-bold transition

                                    {{ $completed
                                        ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20'
                                        : 'bg-white text-black hover:scale-[1.01]'
                                    }}
                                "
                                >

                                    {{ $completed ? 'انجام شد' : 'انجامش دادم!' }}

                                </button>

                            </form>

                        </div>

                    @else

                        {{-- Timer Habit --}}
                        <div
                            x-data="timer(
                            {{ $habit->id }},
                            {{ $target }},
                            '{{ $habit->color }}',
                            {{ $spent }}
                        )"
                            class="space-y-6"
                        >

                            {{-- Progress Header --}}
                            <div class="flex items-center justify-between">

                                <div>

                                    <div class="text-zinc-500 text-sm mb-1">
                                        هدف امروز
                                    </div>

                                    <div class="font-bold text-xl">
                                        {{ $target }} دقیقه
                                    </div>

                                </div>

                                <div class="text-left">

                                    <div class="text-zinc-500 text-sm mb-1">
                                        انجام شده
                                    </div>

                                    <div class="font-bold text-xl">
                                        <span x-text="minutes"></span> دقیقه
                                    </div>

                                </div>

                            </div>

                            {{-- Progress Bar --}}
                            <div class="progress-track h-4">

                                <div
                                    class="progress-fill"
                                    :style="`
                                    width:${progress}% ;
                                    background:${color}
                                `"
                                >
                                </div>

                            </div>

                            {{-- Timer --}}
                            <div class="bg-black/30 border border-white/5 rounded-[28px] p-8 text-center">

                                <div class="text-zinc-500 text-sm mb-3">
                                    تایمر تمرکز
                                </div>

                                <div class="text-6xl font-black tracking-tight mb-8">

                                    <span x-text="formatted"></span>

                                </div>

                                <div class="grid grid-cols-2 gap-4">

                                    <button
                                        @click="toggle"
                                        class="h-14 rounded-2xl bg-white text-black font-bold hover:scale-[1.02] transition"
                                    >

                                    <span x-show="!running">
                                        شروع
                                    </span>

                                        <span x-show="running">
                                        توقف
                                    </span>

                                    </button>

                                    <button
                                        @click="reset"
                                        class="h-14 rounded-2xl border border-white/10 hover:bg-white/5 transition"
                                    >
                                        ریست
                                    </button>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

@endsection
