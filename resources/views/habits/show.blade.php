@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6 mb-8">

            <div class="flex items-center gap-5">

                <div
                    class="w-24 h-24 rounded-[32px] flex items-center justify-center text-5xl"
                    style="
                    background: {{ $habit->color }}20;
                    color: {{ $habit->color }};
                "
                >
                    {{ $habit->icon }}
                </div>

                <div>

                    <div class="flex items-center gap-3 mb-3">

                        <h1 class="text-4xl font-black">
                            {{ $habit->title }}
                        </h1>

                        @if($habit->type === 'timer')

                            <div class="px-4 py-2 rounded-full bg-fuchsia-500/15 text-fuchsia-400 border border-fuchsia-500/20 text-sm">
                                زمان‌دار
                            </div>

                        @else

                            <div class="px-4 py-2 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 text-sm">
                                تیک‌دار
                            </div>

                        @endif

                    </div>

                    <p class="text-zinc-500 leading-8 max-w-2xl">

                        {{ $habit->description ?: 'توضیحی برای این عادت ثبت نشده است.' }}

                    </p>

                </div>

            </div>

            <div class="flex items-center gap-4">

                <a href="{{ route('habits.edit', $habit) }}"
                   class="h-14 px-6 rounded-2xl bg-white text-black flex items-center justify-center font-bold hover:scale-[1.02] transition">

                    ویرایش

                </a>

                <form method="POST"
                      action="{{ route('habits.destroy', $habit) }}">

                    @csrf
                    @method('DELETE')

                    <button
                        class="h-14 px-6 rounded-2xl border border-red-500/20 bg-red-500/10 text-red-400 hover:bg-red-500/20 transition">

                        حذف

                    </button>

                </form>

            </div>

        </div>

        {{-- Top Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

            <div class="glass-card p-6">

                <div class="text-zinc-500 text-sm mb-3">
                    تکرار
                </div>

                <div class="text-3xl font-black">
                    هر {{ $habit->repeat_every }} روز
                </div>

            </div>

            <div class="glass-card p-6">

                <div class="text-zinc-500 text-sm mb-3">
                    تاریخ شروع
                </div>

                <div class="text-3xl font-black">
                    {{ \Morilog\Jalali\Jalalian::fromDateTime($habit->start_date)->format('Y/m/d') }}
                </div>

            </div>

            <div class="glass-card p-6">

                <div class="text-zinc-500 text-sm mb-3">
                    استریک
                </div>

                <div class="text-3xl font-black text-orange-400">
                    {{ $habit->streak() }}
                </div>

            </div>

            <div class="glass-card p-6">

                <div class="text-zinc-500 text-sm mb-3">
                    نرخ موفقیت
                </div>

                <div class="text-3xl font-black text-emerald-400">
                    {{ $habit->successRate() }}%
                </div>

            </div>

        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 2xl:grid-cols-3 gap-6">

            {{-- Left --}}
            <div class="2xl:col-span-2 space-y-6">

                {{-- Timer / Details --}}
                <div class="glass-card p-7">

                    <div class="flex items-center justify-between mb-8">

                        <div>

                            <h2 class="text-2xl font-bold">
                                جزئیات عادت
                            </h2>

                            <p class="text-zinc-500 mt-2">
                                اطلاعات و تنظیمات اصلی
                            </p>

                        </div>

                        <div
                            class="w-5 h-5 rounded-full"
                            style="background: {{ $habit->color }}"
                        >
                        </div>

                    </div>

                    <div class="grid grid-cols-2 xl:grid-cols-4 gap-5">

                        <div class="bg-white/5 rounded-3xl p-5">

                            <div class="text-zinc-500 text-sm mb-2">
                                نوع
                            </div>

                            <div class="font-bold text-lg">
                                {{ $habit->type === 'timer' ? 'زمان‌دار' : 'تیک‌دار' }}
                            </div>

                        </div>

                        <div class="bg-white/5 rounded-3xl p-5">

                            <div class="text-zinc-500 text-sm mb-2">
                                زمان هدف
                            </div>

                            <div class="font-bold text-lg">

                                {{ $habit->target_minutes ?? 0 }}
                                دقیقه

                            </div>

                        </div>

                        <div class="bg-white/5 rounded-3xl p-5">

                            <div class="text-zinc-500 text-sm mb-2">
                                افزایش روزانه
                            </div>

                            <div class="font-bold text-lg">

                                {{ $habit->daily_increment ?? 0 }}
                                دقیقه

                            </div>

                        </div>

                        <div class="bg-white/5 rounded-3xl p-5">

                            <div class="text-zinc-500 text-sm mb-2">
                                Time Block
                            </div>

                            <div class="font-bold text-lg">

                                @if($habit->has_time_block)

                                    {{ $habit->block_time }}

                                @else

                                    ندارد

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Activity --}}
                <div class="glass-card p-7">

                    <div class="flex items-center justify-between mb-8">

                        <div>

                            <h2 class="text-2xl font-bold">
                                فعالیت اخیر
                            </h2>

                            <p class="text-zinc-500 mt-2">
                                آخرین عملکردهای ثبت شده
                            </p>

                        </div>

                    </div>

                    <div class="space-y-4">

                        @forelse($habit->logs->take(7) as $log)

                            <div
                                class="
                                flex items-center justify-between
                                rounded-3xl bg-white/5
                                border border-white/5
                                p-5
                            "
                            >

                                <div>

                                    <div class="font-bold text-lg">

                                        {{ \Morilog\Jalali\Jalalian::fromDateTime($habit->start_date)->format('Y F d') }}

                                    </div>

                                    <div class="text-zinc-500 mt-2 text-sm">

                                        @if($habit->type === 'timer')

                                            {{ $log->spent_minutes }} دقیقه تمرکز

                                        @else

                                            {{ $log->completed ? 'انجام شده' : 'انجام نشده' }}

                                        @endif

                                    </div>

                                </div>

                                @if($log->completed)

                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                                        ✓
                                    </div>

                                @else

                                    <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/10 flex items-center justify-center text-red-400">
                                        ×
                                    </div>

                                @endif

                            </div>

                        @empty

                            <div
                                class="
                                rounded-3xl bg-white/5
                                border border-white/5
                                p-10 text-center
                            "
                            >

                                <div class="text-5xl mb-4">
                                    📭
                                </div>

                                <div class="text-xl font-bold mb-2">
                                    هنوز فعالیتی ثبت نشده
                                </div>

                                <div class="text-zinc-500">
                                    بعد از شروع عادت این بخش تکمیل می‌شود.
                                </div>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

            {{-- Right --}}
            <div class="space-y-6">

                {{-- Progress --}}
                <div class="glass-card p-7">

                    <div class="mb-8">

                        <h2 class="text-2xl font-bold">
                            پیشرفت کلی
                        </h2>

                        <p class="text-zinc-500 mt-2">
                            وضعیت این عادت در طول زمان
                        </p>

                    </div>

                    <div class="flex justify-center mb-8">

                        <div class="relative w-52 h-52">

                            <svg class="w-full h-full rotate-[-90deg]">

                                <circle
                                    cx="104"
                                    cy="104"
                                    r="88"
                                    stroke="rgba(255,255,255,.06)"
                                    stroke-width="18"
                                    fill="none"
                                />

                                <circle
                                    cx="104"
                                    cy="104"
                                    r="88"
                                    stroke="{{ $habit->color }}"
                                    stroke-width="18"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-dasharray="552"
                                    stroke-dashoffset="{{ 552 - (($habit->successRate() / 100) * 552) }}"
                                />

                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center">

                                <div class="text-5xl font-black">

                                    {{ $habit->successRate() }}%

                                </div>

                                <div class="text-zinc-500 mt-2">
                                    موفقیت
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="space-y-4">

                        <div class="flex items-center justify-between">

                        <span class="text-zinc-500">
                            کل دفعات انجام
                        </span>

                            <span class="font-bold text-xl">
                            {{ $habit->logs->where('completed', true)->count() }}
                        </span>

                        </div>

                        <div class="flex items-center justify-between">

                        <span class="text-zinc-500">
                            کل روزها
                        </span>

                            <span class="font-bold text-xl">
                            {{ $habit->logs->count() }}
                        </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
