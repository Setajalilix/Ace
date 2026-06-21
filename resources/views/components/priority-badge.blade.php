@props(['priority'])

@php $p = $priority instanceof \App\Domains\Tasks\Enums\TaskPriority ? $priority : \App\Domains\Tasks\Enums\TaskPriority::from((int) $priority); @endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full border '.$p->bgClass()]) }}>
    <x-icon :name="$p->icon()" class="w-3.5 h-3.5" />
    {{ $p->shortLabel() }}
</span>
