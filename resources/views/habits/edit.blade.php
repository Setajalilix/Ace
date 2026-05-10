@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">

            <div>

                <h1 class="text-4xl font-black">
                    ویرایش عادت
                </h1>

                <p class="text-zinc-500 mt-3">
                    تنظیمات عادتت را بروزرسانی کن
                </p>

            </div>

            <a href="{{ route('habits.show', $habit) }}"
               class="
                h-14 px-6 rounded-2xl
                border border-white/10
                hover:bg-white/5
                transition flex items-center
           ">

                بازگشت

            </a>

        </div>

        <form
            method="POST"
            action="{{ route('habits.update', $habit) }}"
            x-data="{
            type: '{{ old('type', $habit->type) }}'
        }"

            class="grid grid-cols-1 xl:grid-cols-3 gap-6"
        >

            @csrf
            @method('PUT')

            {{-- Left --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Main --}}
                <div class="glass-card p-6">

                    <h2 class="text-2xl font-bold mb-6">
                        اطلاعات اصلی
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Title --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm text-zinc-400 mb-3">
                                عنوان
                            </label>

                            <input
                                type="text"
                                name="title"
                                value="{{ old('title', $habit->title) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                            "
                            >

                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm text-zinc-400 mb-3">
                                توضیحات
                            </label>

                            <textarea
                                name="description"
                                rows="3"

                                class="
                                w-full p-5 rounded-2xl
                                bg-white/5 border border-white/10
                                focus:outline-none
                                focus:ring-2 focus:ring-indigo-500/30
                                resize-none
                            "
                            >{{ old('description', $habit->description) }}</textarea>

                        </div>

                        {{-- Icon --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                آیکون
                            </label>

                            <input
                                type="text"
                                name="icon"
                                value="{{ old('icon', $habit->icon) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
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
                                value="{{ old('color', $habit->color) }}"

                                class="
                                w-full h-14 p-2 rounded-2xl
                                bg-white/5 border border-white/10
                            "
                            >

                        </div>

                    </div>

                </div>

                {{-- Type --}}
                <div class="glass-card p-6">

                    <h2 class="text-2xl font-bold mb-6">
                        نوع عادت
                    </h2>

                    <div class="grid grid-cols-2 gap-5">

                        {{-- Checkbox --}}
                        <label
                            @click="type = 'checkbox'"

                            class="
                            cursor-pointer rounded-3xl
                            border p-5 transition

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

                                :checked="type === 'checkbox'"
                            >

                            <div class="flex items-center gap-4">

                                <div
                                    class="
                                    w-14 h-14 rounded-2xl
                                    bg-emerald-500/15
                                    flex items-center justify-center
                                "
                                >
                                    ✓
                                </div>

                                <div>

                                    <div class="font-bold text-lg">
                                        تیک‌دار
                                    </div>

                                    <div class="text-zinc-500 text-sm mt-1">
                                        فقط ثبت انجام
                                    </div>

                                </div>

                            </div>

                        </label>

                        {{-- Timer --}}
                        <label
                            @click="type = 'timer'"

                            class="
                            cursor-pointer rounded-3xl
                            border p-5 transition
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

                                :checked="type === 'timer'"
                            >

                            <div class="flex items-center gap-4">

                                <div
                                    class="
                                    w-14 h-14 rounded-2xl
                                    bg-fuchsia-500/15
                                    flex items-center justify-center
                                "
                                >
                                    ⏱
                                </div>

                                <div>

                                    <div class="font-bold text-lg">
                                        زمان‌دار
                                    </div>

                                    <div class="text-zinc-500 text-sm mt-1">
                                        بر اساس تایمر
                                    </div>

                                </div>

                            </div>

                        </label>

                    </div>

                </div>

            </div>

            {{-- Right --}}
            <div class="space-y-6">

                {{-- Settings --}}
                <div class="glass-card p-6">

                    <h2 class="text-2xl font-bold mb-6">
                        تنظیمات
                    </h2>

                    <div class="space-y-5">

                        {{-- Repeat --}}
                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                تکرار
                            </label>

                            <input
                                type="number"
                                name="repeat_every"
                                min="1"
                                value="{{ old('repeat_every', $habit->repeat_every) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
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
                                value="{{ old('start_date', $habit->start_date?->format('Y-m-d')) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
                            "
                            >

                        </div>

                        {{-- TIMER ONLY --}}
                        <div
                            x-show="type === 'timer'"
                            x-transition
                            class="space-y-5"
                        >

                            {{-- Target --}}
                            <div>

                                <label class="block text-sm text-zinc-400 mb-3">
                                    زمان اولیه
                                </label>

                                <input
                                    type="number"
                                    name="target_minutes"
                                    value="{{ old('target_minutes', $habit->target_minutes) }}"

                                    class="
                                    w-full h-14 px-5 rounded-2xl
                                    bg-white/5 border border-white/10
                                "
                                >

                            </div>

                            {{-- Increment --}}
                            <div>

                                <label class="block text-sm text-zinc-400 mb-3">
                                    افزایش روزانه
                                </label>

                                <input
                                    type="number"
                                    name="daily_increment"
                                    value="{{ old('daily_increment', $habit->daily_increment) }}"

                                    class="
                                    w-full h-14 px-5 rounded-2xl
                                    bg-white/5 border border-white/10
                                "
                                >

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Time Block --}}
                <div class="glass-card p-6">

                    <h2 class="text-2xl font-bold mb-6">
                        Time Block
                    </h2>

                    <div class="space-y-5">

                        <label
                            class="
                            flex items-center justify-between
                            rounded-2xl bg-white/5
                            border border-white/10 p-4
                        "
                        >

                        <span>
                            فعال‌سازی
                        </span>

                            <input
                                type="checkbox"
                                name="has_time_block"
                                value="1"

                                @checked($habit->has_time_block)

                                class="w-5 h-5"
                            >

                        </label>

                        <div>

                            <label class="block text-sm text-zinc-400 mb-3">
                                ساعت انجام
                            </label>

                            <input
                                type="time"
                                name="block_time"
                                value="{{ old('block_time', $habit->block_time) }}"

                                class="
                                w-full h-14 px-5 rounded-2xl
                                bg-white/5 border border-white/10
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

                    ذخیره تغییرات

                </button>

            </div>

        </form>

    </div>

@endsection
