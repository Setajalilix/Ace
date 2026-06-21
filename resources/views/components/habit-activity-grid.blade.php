@props(['grid', 'color' => '#7BAE7F', 'large' => false])

<div class="flex flex-wrap {{ $large ? 'gap-1.5' : 'gap-1' }}">
    @foreach($grid as $cell)
        @php
            $opacity = match($cell['level']) {
                4 => '1',
                3 => '0.75',
                2 => '0.55',
                1 => '0.35',
                default => '0.12',
            };
            $size = $large ? 'w-4 h-4 sm:w-[18px] sm:h-[18px] rounded-md' : 'w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-sm';
        @endphp
        <span title="{{ $cell['date'] }}"
              class="{{ $size }} border border-[#EDE5DA]/80 transition-transform hover:scale-110"
              style="background-color: {{ $color }}; opacity: {{ $opacity }}"></span>
    @endforeach
</div>
