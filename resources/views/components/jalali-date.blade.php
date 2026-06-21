@props(['date'])

@php
    $jalali = app(\App\Shared\Calendar\JalaliDateService::class)->format($date);
@endphp

<span {{ $attributes->merge(['class' => 'text-zinc-500']) }}>{{ $jalali }}</span>
