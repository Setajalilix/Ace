@props(['status'])

@php $s = $status instanceof \App\Domains\Tasks\Enums\TaskStatus ? $status : \App\Domains\Tasks\Enums\TaskStatus::from($status); @endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full border '.$s->bgClass()]) }}>
    <x-icon :name="$s->icon()" class="w-3.5 h-3.5" />
    {{ $s->label() }}
</span>
