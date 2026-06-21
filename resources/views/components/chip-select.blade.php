@props(['name', 'label', 'options' => [], 'value' => null, 'nullable' => false, 'collapsibleOnMobile' => false])

@php
    $optionLabels = collect($options)->mapWithKeys(fn ($o) => [(string) $o['value'] => $o['label']])->all();
@endphp

<div x-data="{
        selected: '{{ old($name, $value ?? '') }}',
        open: false,
        labels: @js($optionLabels),
        nullable: {{ $nullable ? 'true' : 'false' }},
        get summary() {
            if (this.selected === '' && this.nullable) return 'None';
            return this.labels[this.selected] ?? '';
        }
    }"
     data-chip-field="{{ $name }}"
     @ace:chip-set.window="if ($event.detail.name === '{{ $name }}') selected = $event.detail.value"
     class="space-y-2">
    @if($label)
        @if($collapsibleOnMobile)
            <button type="button" @click="open = !open"
                    class="sm:hidden flex w-full items-center justify-between gap-3 py-1.5 -mx-0.5 rounded-lg active:bg-[#F3EDE4]/60">
                <span class="text-sm font-medium text-[#6B5B4F]">{{ $label }}</span>
                <span class="flex items-center gap-1.5 min-w-0">
                    <span class="text-xs text-[#A8958B] truncate max-w-[9rem]" x-text="summary"></span>
                    <svg class="w-4 h-4 text-[#A8958B] shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </span>
            </button>
            <label class="hidden sm:block text-sm font-medium text-[#6B5B4F]">{{ $label }}</label>
        @else
            <label class="block text-sm font-medium text-[#6B5B4F]">{{ $label }}</label>
        @endif
    @endif
    <input type="hidden" name="{{ $name }}" :value="selected">
    <div @if($collapsibleOnMobile) x-show="open" x-cloak data-chip-options @endif
         class="flex flex-wrap gap-2">
        @if($nullable)
            <button type="button" @click="selected = ''"
                    :class="selected === '' ? 'bg-[#3D3229] text-white border-[#3D3229]' : 'bg-white text-[#6B5B4F] border-[#E8DDD4] hover:border-[#C47D5A]'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border transition-all">None</button>
        @endif
        @foreach($options as $option)
            <button type="button" @click="selected = '{{ $option['value'] }}'"
                    :class="selected == '{{ $option['value'] }}' ? '{{ $option['active'] ?? 'bg-[#C47D5A] text-white border-[#C47D5A]' }}' : 'bg-white text-[#6B5B4F] border-[#E8DDD4] hover:border-[#C47D5A]'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border transition-all inline-flex items-center gap-1">
                @if(!empty($option['dot']))
                    <span class="w-2 h-2 rounded-full" style="background: {{ $option['dot'] }}"></span>
                @endif
                {{ $option['label'] }}
            </button>
        @endforeach
    </div>
</div>
