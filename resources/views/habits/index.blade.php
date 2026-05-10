@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div>

                <h1 class="text-4xl font-black">
                    عادت‌های من
                </h1>

                <p class="text-zinc-500 mt-3 leading-8">

                    عادت‌هایی که امروز،
                    آینده تو را می‌سازند.

                </p>

            </div>

            <div class="flex items-center gap-4">

                {{-- Search --}}
                <div class="relative hidden md:block">

                    <input
                        type="text"
                        placeholder="جستجوی عادت..."

                        class="
                        w-72 h-14 pr-5 pl-12 rounded-2xl
                        bg-white/5 border border-white/10
                        focus:outline-none focus:ring-2
                        focus:ring-indigo-500/40
                        placeholder:text-zinc-600
                    "
                    >

                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500">
                        ⌕
                    </div>

                </div>

                {{-- Create --}}
                <a
                    href="{{ route('habits.create') }}"
                    class="
                    h-14 px-7 rounded-2xl
                    bg-white text-black
                    font-bold flex items-center justify-center
                    hover:scale-[1.02] transition
                "
                >

                    + عادت جدید

                </a>

            </div>

        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-5">

            <div class="glass-card p-5">

                <div class="text-zinc-500 text-sm mb-2">
                    کل عادت‌ها
                </div>

                <div class="text-4xl font-black">
                    {{ $habits->count() }}
                </div>

            </div>

            <div class="glass-card p-5">

                <div class="text-zinc-500 text-sm mb-2">
                    عادت‌های زمان‌دار
                </div>

                <div class="text-4xl font-black text-fuchsia-400">
                    {{ $habits->where('type', 'timer')->count() }}
                </div>

            </div>

            <div class="glass-card p-5">

                <div class="text-zinc-500 text-sm mb-2">
                    عادت‌های تیک‌دار
                </div>

                <div class="text-4xl font-black text-emerald-400">
                    {{ $habits->where('type', 'checkbox')->count() }}
                </div>

            </div>

            <div class="glass-card p-5">

                <div class="text-zinc-500 text-sm mb-2">
                    دارای Time Block
                </div>

                <div class="text-4xl font-black text-orange-400">
                    {{ $habits->where('has_time_block', true)->count() }}
                </div>

            </div>

        </div>

        {{-- Habits --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">

            @forelse($habits as $habit)

                <div class="habit-card p-6 relative overflow-hidden">

                    {{-- Top Border --}}
                    <div
                        class="absolute top-0 left-0 w-full h-1"
                        style="background: {{ $habit->color }}"
                    >
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

                                <h2 class="text-xl font-bold mb-2">
                                    {{ $habit->title }}
                                </h2>

                                <div class="flex items-center gap-2 text-sm text-zinc-500">

                                    @if($habit->type === 'timer')

                                        <span class="text-fuchsia-400">
                                        زمان‌دار
                                    </span>

                                    @else

                                        <span class="text-emerald-400">
                                        تیک‌دار
                                    </span>

                                    @endif

                                    <span class="w-1 h-1 rounded-full bg-zinc-600"></span>

                                    <span>
                                    هر {{ $habit->repeat_every }} روز
                                </span>

                                </div>

                            </div>

                        </div>

                        @if($habit->has_time_block)

                            <div
                                class="
                                px-3 py-2 rounded-2xl
                                bg-orange-500/10
                                border border-orange-500/10
                                text-orange-400 text-sm
                            "
                            >

                                {{ $habit->block_time }}

                            </div>

                        @endif

                    </div>

                    {{-- Description --}}
                    <p class="text-zinc-500 leading-8 text-sm mb-8 line-clamp-2 min-h-[56px]">

                        {{ $habit->description ?: 'توضیحی برای این عادت ثبت نشده است.' }}

                    </p>

                    {{-- Progress --}}
                    @if($habit->type === 'timer')

                        @php
                            $todayTarget = $habit->todayTargetMinutes();
                        @endphp

                        <div class="mb-7">

                            <div class="flex items-center justify-between mb-3">

                            <span class="text-sm text-zinc-500">
                                هدف امروز
                            </span>

                                <span class="font-bold">

                                {{ $todayTarget }} دقیقه

                            </span>

                            </div>

                            <div class="progress-track">

                                <div
                                    class="progress-fill"
                                    style="
                                    width: 65%;
                                    background: {{ $habit->color }}
                                "
                                >
                                </div>

                            </div>

                        </div>

                    @else

                        <div class="mb-7">

                            <div class="flex items-center justify-between mb-3">

                            <span class="text-sm text-zinc-500">
                                وضعیت
                            </span>

                                <span class="font-bold text-emerald-400">
                                فعال
                            </span>

                            </div>

                            <div class="progress-track">

                                <div
                                    class="progress-fill bg-emerald-400"
                                    style="width: 85%"
                                >
                                </div>

                            </div>

                        </div>

                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center gap-3">

                        <a
                            href="{{ route('habits.show', $habit) }}"
                            class="
                            flex-1 h-14 rounded-2xl
                            bg-white text-black
                            flex items-center justify-center
                            font-bold hover:scale-[1.01]
                            transition
                        "
                        >

                            مشاهده

                        </a>

                        <a
                            href="{{ route('habits.edit', $habit) }}"
                            class="
                            w-14 h-14 rounded-2xl
                            border border-white/10
                            hover:bg-white/5
                            flex items-center justify-center
                            transition
                        "
                        >

                            ✎

                        </a>

                    </div>

                </div>

            @empty

                {{-- Empty State --}}
                <div class="col-span-full">

                    <div
                        class="
                        glass-card p-16
                        text-center
                    "
                    >

                        <div class="text-7xl mb-6">
                            🪴
                        </div>

                        <h2 class="text-3xl font-black mb-4">
                            هنوز عادتی نساختی
                        </h2>

                        <p class="text-zinc-500 max-w-xl mx-auto leading-8 mb-8">

                            اولین عادتت را بساز و
                            مسیر دیسیپلین شخصی خودت را شروع کن.

                        </p>

                        <a
                            href="{{ route('habits.create') }}"
                            class="
                            inline-flex h-14 px-8 rounded-2xl
                            bg-white text-black
                            items-center justify-center
                            font-bold hover:scale-[1.02]
                            transition
                        "
                        >

                            ساخت اولین عادت

                        </a>

                    </div>

                </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        @if(method_exists($habits, 'links'))

            <div class="pt-4">

                {{ $habits->links() }}

            </div>

        @endif

    </div>

@endsection
