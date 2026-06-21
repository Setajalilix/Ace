@props(['status'])

@php $s = $status instanceof \App\Domains\Events\Enums\EventStatus ? $status : \App\Domains\Events\Enums\EventStatus::from($status); @endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full border '.$s->bgClass()]) }}>
    <x-icon :name="$s->icon()" class="w-3 h-3" />
    {{ $s->label() }}
</span>
