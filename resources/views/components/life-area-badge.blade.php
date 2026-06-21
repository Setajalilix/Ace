@props(['area'])

@if($area)
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full border border-[#E8DDD4] bg-white text-[#6B5B4F]']) }}>
    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $area->color }}"></span>
    {{ $area->name }}
</span>
@endif
