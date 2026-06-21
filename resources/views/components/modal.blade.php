@props(['show' => 'open', 'wide' => false, 'compact' => false, 'close' => null])

@php
    $widthClass = $wide ? 'max-w-lg' : 'max-w-md';
    $closeAction = $close ?? ($show.' = false');
@endphp

<div x-show="{{ $show }}" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" @keydown.escape.window="{{ $closeAction }}">
    <div class="fixed inset-0 bg-[#3D3229]/40 backdrop-blur-[2px] transition-opacity" @click="{{ $closeAction }}"></div>
    <div class="flex min-h-full items-end sm:items-center justify-center px-5 py-6 pb-24 sm:px-6 sm:py-8 pointer-events-none">
        <div {{ $attributes->merge(['class' => "relative w-full {$widthClass} pointer-events-auto"]) }} @click.stop @mousedown.stop @touchstart.stop>
            <div class="ace-modal-panel max-h-[min(calc(100dvh-4rem),92dvh)] flex flex-col">
                <div class="ace-modal-scroll flex-1 overflow-y-auto overscroll-contain p-5 sm:p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
