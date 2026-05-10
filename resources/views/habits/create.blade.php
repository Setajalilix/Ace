@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">

            <div>

                <h1 class="text-4xl font-black">
                    ساخت عادت جدید
                </h1>

                <p class="text-zinc-500 mt-3">
                    امروز چیزی بساز که آینده‌ات از تو تشکر کند.
                </p>

            </div>

            <a
                href="{{ route('habits.index') }}"

                class="
                h-14 px-6 rounded-2xl
                border border-white/10
                hover:bg-white/5
                transition
                flex items-center
            "
            >

                بازگشت

            </a>

        </div>

        <form
            method="POST"
            action="{{ route('habits.store') }}"

            x-data="{
            type: 'checkbox'
        }"

            class="grid grid-cols-1 xl:grid-cols-3 gap-6"
        >

            @csrf

            {{-- LEFT --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Main Info --}}
                <div class="glass-card p-7">

                    <div class="mb-7">

                        <h2 class="text-2xl font-bold mb-2">
                            اطلاعات اصلی
                        </h2>

                        <p class="text-zinc-500 text-sm">
                            مشخصات پایه عادت را وارد کن
                        </p>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Title --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm text-zinc-400 mb-3">
                                عنوان عادت
                            </label>

                            <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="مثلاً مطالعه کتاب"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                                placeholder:text-zinc-600
                            "
                            >

                            @error('title')

                            <p class="text-red-400 text-sm mt-2">
                                {{ $message }}
                            </p>

                            @enderror

                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm text-zinc-400 mb-3">
                                توضیحات
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                placeholder="توضیح کوتاه درباره این عادت..."

                                class="
                                w-full p-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                                resize-none
                                placeholder:text-zinc-600
                            "
                            >{{ old('description') }}</textarea>

                        </div>

                        {{-- Icon --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                آیکون
                            </label>

                            <input
                                type="text"
                                name="icon"
                                value="{{ old('icon') }}"
                                placeholder="📚"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                            "
                            >

                        </div>

                        {{-- Color --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                رنگ
                            </label>

                            <input
                                type="color"
                                name="color"
                                value="{{ old('color', '#6366f1') }}"

                                class="
                                w-full h-14 rounded-2xl
                                bg-white/5 border border-white/10
                                p-2 cursor-pointer
                            "
                            >

                        </div>

                    </div>

                </div>

                {{-- Type --}}
                <div class="glass-card p-7">

                    <div class="mb-7">

                        <h2 class="text-2xl font-bold mb-2">
                            نوع عادت
                        </h2>

                        <p class="text-zinc-500 text-sm">
                            نوع سیستم پیگیری عادت را انتخاب کن
                        </p>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Checkbox --}}
                        <label
                            @click="type = 'checkbox'"

                            class="
                            rounded-3xl border p-6
                            cursor-pointer transition
                        "

                            :class="
                            type === 'checkbox'
                            ? 'border-emerald-500/30 bg-emerald-500/10'
                            : 'border-white/10 bg-white/5'
                        "
                        >

                            <input
                                type="radio"
                                name="type"
                                value="checkbox"
                                class="hidden"

                                checked
                            >

                            <div class="flex items-center gap-5">

                                <div
                                    class="
                                    w-16 h-16 rounded-3xl
                                    bg-emerald-500/15
                                    flex items-center justify-center
                                    text-2xl
                                "
                                >
                                    ✓
                                </div>

                                <div>

                                    <div class="text-xl font-bold mb-2">
                                        عادت تیک‌دار
                                    </div>

                                    <p class="text-zinc-500 text-sm leading-7">

                                        فقط کافیست انجام شدن عادت را ثبت کنی

                                    </p>

                                </div>

                            </div>

                        </label>

                        {{-- Timer --}}
                        <label
                            @click="type = 'timer'"

                            class="
                            rounded-3xl border p-6
                            cursor-pointer transition
                        "

                            :class="
                            type === 'timer'
                            ? 'border-fuchsia-500/30 bg-fuchsia-500/10'
                            : 'border-white/10 bg-white/5'
                        "
                        >

                            <input
                                type="radio"
                                name="type"
                                value="timer"
                                class="hidden"
                            >

                            <div class="flex items-center gap-5">

                                <div
                                    class="
                                    w-16 h-16 rounded-3xl
                                    bg-fuchsia-500/15
                                    flex items-center justify-center
                                    text-2xl
                                "
                                >
                                    ⏱
                                </div>

                                <div>

                                    <div class="text-xl font-bold mb-2">
                                        عادت زمان‌دار
                                    </div>

                                    <p class="text-zinc-500 text-sm leading-7">

                                        عادت بر اساس زمان انجام می‌شود

                                    </p>

                                </div>

                            </div>

                        </label>

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- Settings --}}
                <div class="glass-card p-7">

                    <div class="mb-7">

                        <h2 class="text-2xl font-bold mb-2">
                            تنظیمات
                        </h2>

                        <p class="text-zinc-500 text-sm">
                            تنظیمات تکرار و زمان‌بندی
                        </p>

                    </div>

                    <div class="space-y-5">

                        {{-- Repeat --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                تکرار هر چند روز
                            </label>

                            <input
                                type="number"
                                name="repeat_every"
                                min="1"

                                value="{{ old('repeat_every', 1) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                            "
                            >

                        </div>

                        {{-- Start Date --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                تاریخ شروع
                            </label>

                            <input
                                type="date"
                                name="start_date"

                                value="{{ old('start_date', now()->format('Y-m-d')) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                            "
                            >

                        </div>

                        {{-- TIMER ONLY --}}
                        <div
                            x-show="type === 'timer'"
                            x-transition.opacity
                            class="space-y-5"
                        >

                            {{-- Target Minutes --}}
                            <div>

                                <label class="block text-sm text-zinc-400 mb-3">
                                    زمان اولیه (دقیقه)
                                </label>

                                <input
                                    type="number"
                                    name="target_minutes"

                                    value="{{ old('target_minutes', 20) }}"

                                    class="
                                    w-full h-14 px-5 rounded-2xl
                                    bg-white/5 border border-white/10
                                    focus:outline-none
                                    focus:ring-2 focus:ring-indigo-500/30
                                "
                                >

                            </div>

                            {{-- Daily Increment --}}
                            <div>

                                <label class="block text-sm text-zinc-400 mb-3">
                                    افزایش روزانه (دقیقه)
                                </label>

                                <input
                                    type="number"
                                    name="daily_increment"

                                    value="{{ old('daily_increment', 0) }}"

                                    class="
                                    w-full h-14 px-5 rounded-2xl
                                    bg-white/5 border border-white/10
                                    focus:outline-none
                                    focus:ring-2 focus:ring-indigo-500/30
                                "
                                >

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Time Block --}}
                <div class="glass-card p-7">

                    <div class="mb-7">

                        <h2 class="text-2xl font-bold mb-2">
                            Time Block
                        </h2>

                        <p class="text-zinc-500 text-sm">
                            زمان مشخص برای انجام عادت
                        </p>

                    </div>

                    <div class="space-y-5">

                        {{-- Enable --}}
                        <label
                            class="
                            flex items-center justify-between
                            p-4 rounded-2xl
                            bg-white/5 border border-white/10
                        "
                        >

                        <span class="font-medium">
                            فعال‌سازی Time Block
                        </span>

                            <input
                                type="checkbox"
                                name="has_time_block"
                                value="1"

                                class="w-5 h-5"
                            >

                        </label>

                        {{-- Time --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                ساعت انجام
                            </label>

                            <input
                                type="time"
                                name="block_time"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                            "
                            >

                        </div>

                    </div>

                </div>

                {{-- Submit --}}
                <button
                    class="
                    w-full h-16 rounded-3xl
                    bg-white text-black
                    font-black text-lg
                    hover:scale-[1.01]
                    transition
                "
                >

                    ذخیره عادت

                </button>

            </div>

        </form>

    </div>

@endsection
