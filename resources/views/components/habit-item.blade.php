@props(['habit', 'log' => null, 'compact' => false, 'autoSave' => true, 'hideTitle' => false, 'alignEnd' => false, 'checkboxOnly' => false])

@php
    $log = $log ?? $habit->logs->first();
    $color = $habit->color ?? '#7BAE7F';
    $current = $log?->count ?? 0;
    $target = $habit->type === 'counter' ? $habit->todayTargetCount() : 0;
    $pct = $habit->type === 'counter' ? min(100, ($target > 0 ? ($current / $target) * 100 : 0)) : 0;
    $spent = $log?->spent_minutes ?? 0;
    $targetMin = $habit->type === 'timer' ? $habit->todayTargetMinutes() : 0;
    $config = [
        'id' => $habit->id,
        'type' => $habit->type,
        'completed' => (bool) ($log?->completed),
        'count' => $current,
        'target' => $target,
        'pct' => $pct,
    ];
@endphp

<div class="{{ $compact && !$checkboxOnly ? 'py-2' : ($checkboxOnly ? '' : 'p-3 rounded-xl border border-[#EDE5DA] bg-white mb-2') }}"
     @if($habit->type !== 'timer' && !$checkboxOnly) x-data="habitInteract(@js($config))" @endif>
    @if($habit->type === 'checkbox')
        @if($checkboxOnly)
        <div x-data="habitInteract(@js($config))">
            <button type="button" @click="toggleCheckbox()" :disabled="completing"
                    class="w-6 h-6 rounded-full border-2 flex-shrink-0 transition-all duration-200"
                    :class="completed ? 'bg-[#7BAE7F] border-[#7BAE7F] check-pop' : (completing ? 'border-[#7BAE7F] scale-110' : 'border-[#D4C4B5] hover:border-[#7BAE7F]')"></button>
        </div>
        @else
        <div class="flex items-center gap-3 {{ $hideTitle ? 'justify-end' : '' }}">
            @unless($hideTitle)
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $color }}"></span>
            <span class="text-sm font-medium flex-1 truncate transition-all duration-300"
                  :class="completed ? 'line-through text-[#A8958B]' : 'text-[#3D3229]'">{{ $habit->title }}</span>
            @endunless
            <button type="button" @click="toggleCheckbox()" :disabled="completing"
                    class="w-6 h-6 rounded-full border-2 flex-shrink-0 transition-all duration-200 {{ $hideTitle ? '' : 'ml-auto' }}"
                    :class="completed ? 'bg-[#7BAE7F] border-[#7BAE7F] check-pop' : (completing ? 'border-[#7BAE7F] scale-110' : 'border-[#D4C4B5] hover:border-[#7BAE7F]')"></button>
        </div>
        @endif

    @elseif($habit->type === 'counter')
        @if($alignEnd)
        <div class="flex items-center justify-end gap-2 flex-wrap">
            <span class="text-xs font-semibold tabular-nums transition-colors duration-300"
                  :class="completed ? 'text-[#7BAE7F]' : 'text-[#A8958B]'"
                  x-text="`${count}/${target}`">{{ $current }}/{{ $target }}</span>
            <div class="w-14 h-1.5 bg-[#F3EDE4] rounded-full overflow-hidden hidden xs:block">
                <div class="h-full rounded-full transition-all duration-500 ease-out" :style="`width: ${pct}%; background: {{ $color }}`"></div>
            </div>
            <button type="button" @click="incrementCounter()"
                    class="w-8 h-8 rounded-lg bg-[#F3EDE4] hover:bg-[#EDE5DA] active:scale-90 text-[#3D3229] font-bold text-lg leading-none transition-transform">+</button>
            <form @submit="setCounter($event)" class="flex items-center gap-1">
                <input type="number" name="count" :value="count" min="0" class="input text-sm py-1.5 w-14 text-center">
                <button type="submit" class="btn-secondary text-xs px-2 py-1.5" :disabled="saving">Set</button>
            </form>
        </div>
        @else
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                @unless($hideTitle)
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $color }}"></span>
                <span class="text-sm font-medium text-[#3D3229] flex-1 truncate">{{ $habit->title }}</span>
                @endunless
                <span class="text-xs font-semibold tabular-nums transition-colors duration-300 ml-auto"
                      :class="completed ? 'text-[#7BAE7F]' : 'text-[#A8958B]'"
                      x-text="`${count}/${target}`">{{ $current }}/{{ $target }}</span>
            </div>
            <div class="h-1.5 bg-[#F3EDE4] rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 ease-out" :style="`width: ${pct}%; background: {{ $color }}`"></div>
            </div>
            <div class="flex items-center gap-2 justify-end">
                <button type="button" @click="incrementCounter()"
                        class="w-8 h-8 rounded-lg bg-[#F3EDE4] hover:bg-[#EDE5DA] active:scale-90 text-[#3D3229] font-bold text-lg leading-none transition-transform">+</button>
                <form @submit="setCounter($event)" class="flex items-center gap-1">
                    <input type="number" name="count" :value="count" min="0" class="input text-sm py-1.5 w-16 text-center">
                    <button type="submit" class="btn-secondary text-xs px-2 py-1.5" :disabled="saving">Set</button>
                </form>
            </div>
        </div>
        @endif

    @else
        <div x-data="habitTimer({{ $habit->id }}, {{ $spent }})" x-init="targetMinutes = {{ $targetMin }}" class="{{ $hideTitle ? 'space-y-0' : 'space-y-3' }}">
            @unless($hideTitle)
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $color }}"></span>
                <a href="{{ route('habits.show', $habit) }}" class="text-sm font-medium text-[#3D3229] flex-1 truncate hover:text-[#C47D5A]">{{ $habit->title }}</a>
            </div>
            @endunless
            <div class="flex items-center gap-3 justify-end">
                <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center shrink-0">
                    <svg class="absolute inset-0 -rotate-90 w-full h-full" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="#F3EDE4" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="{{ $color }}" stroke-width="3"
                                stroke-dasharray="97.4" :stroke-dashoffset="97.4 - (97.4 * pct / 100)" stroke-linecap="round"
                                class="transition-all duration-300" :class="running && 'opacity-90'"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-semibold font-mono text-[#3D3229] relative z-10" x-text="displayTime">{{ sprintf('%02d:%02d', intdiv($spent, 60), $spent % 60) }}</span>
                </div>
                <div class="text-left shrink-0">
                    <p class="text-xs text-[#A8958B]">Target <span x-text="targetMinutes">{{ $targetMin }}</span> min</p>
                    <p class="text-xs text-[#A8958B]">Logged <span x-text="loggedDisplay">{{ $spent }} min</span></p>
                    <div class="flex gap-1 mt-2">
                        <button type="button" @click="toggle()" class="btn-primary text-xs px-3 py-1.5" x-text="running ? 'Pause' : 'Start'"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
