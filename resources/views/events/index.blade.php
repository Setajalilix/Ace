@extends('layouts.app')
@section('title', 'Events — '.config('app.name'))
@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="page-title">Events</h1>
        <p class="text-sm text-[#A8958B] mt-1">Appointments, meetings, and recurring events</p>
    </div>
    <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-event-create'))" class="btn-primary">
        <x-icon name="plus" class="w-4 h-4" /> New Event
    </button>
</div>

<div class="space-y-3">
    @forelse($events as $event)
        @php
            $eventData = [
                'id' => $event->id,
                'title' => $event->title,
                'location' => $event->location,
                'start_date' => $event->starts_at->toDateString(),
                'start_time' => $event->starts_at->format('H:i'),
                'end_date' => $event->ends_at?->toDateString(),
                'end_time' => $event->ends_at?->format('H:i'),
                'life_area_id' => $event->life_area_id,
                'recurrence' => $event->recurrence_rule ? (json_decode($event->recurrence_rule, true)['type'] ?? 'none') : 'none',
                'status' => $event->status->value,
            ];
        @endphp
        <button type="button" @click="window.dispatchEvent(new CustomEvent('ace:open-event-edit', { detail: @js($eventData) }))"
                class="card flex flex-col sm:flex-row sm:items-center gap-3 w-full text-left hover:border-[#C47D5A]/30 transition-all">
            <div class="w-10 h-10 rounded-xl bg-[#6B9BD1]/15 flex items-center justify-center flex-shrink-0">
                <x-icon name="calendar" class="w-5 h-5 text-[#6B9BD1]" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-[#3D3229]">{{ $event->title }}</p>
                <p class="text-xs text-[#A8958B] mt-0.5">
                    {{ $event->starts_at->format('M j, H:i') }} · <x-jalali-date :date="$event->starts_at" />
                    @if($event->recurrence_rule) · {{ $event->recurrenceLabel() }}@endif
                </p>
            </div>
            @if($event->lifeArea)<x-life-area-badge :area="$event->lifeArea" />@endif
            @if($event->status !== \App\Domains\Events\Enums\EventStatus::Scheduled)
                <x-event-status-badge :status="$event->status" />
            @endif
        </button>
    @empty
        <div class="card text-center py-12 text-[#A8958B]">No events scheduled. Create your first event above.</div>
    @endforelse
    {{ $events->links() }}
</div>
@endsection
