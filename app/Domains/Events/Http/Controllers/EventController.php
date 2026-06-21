<?php

namespace App\Domains\Events\Http\Controllers;

use App\Domains\Events\Enums\EventStatus;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Events\Http\Requests\StoreEventRequest;
use App\Domains\Events\Http\Requests\UpdateEventRequest;
use App\Domains\Events\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = $request->user()->events()
            ->with('lifeArea')
            ->latest('starts_at')
            ->paginate(20);

        return view('events.index', compact('events'));
    }

    public function store(StoreEventRequest $request)
    {
        $event = $request->user()->events()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'location' => $request->validated('location'),
            'starts_at' => $request->startsAt(),
            'ends_at' => $request->endsAt(),
            'life_area_id' => $request->validated('life_area_id'),
            'recurrence_rule' => $this->encodeRecurrence($request),
            'status' => EventStatus::Scheduled,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event created.',
                'event' => $this->eventPayload($event->fresh('lifeArea')),
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Event created.');
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        abort_unless($event->user_id === $request->user()->id, 403);

        $event->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'location' => $request->validated('location'),
            'starts_at' => $request->startsAt(),
            'ends_at' => $request->endsAt(),
            'life_area_id' => $request->validated('life_area_id'),
            'recurrence_rule' => $this->encodeRecurrence($request),
            'status' => EventStatus::from($request->validated('status') ?? $event->status->value),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Event updated.',
                'event' => $this->eventPayload($event->fresh('lifeArea')),
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function destroy(Request $request, Event $event)
    {
        abort_unless($event->user_id === $request->user()->id, 403);
        $event->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Event deleted.']);
        }

        return back()->with('success', 'Event deleted.');
    }

    private function encodeRecurrence(StoreEventRequest|UpdateEventRequest $request): ?string
    {
        $recurrence = $request->validated('recurrence');

        if (! $recurrence || $recurrence === 'none') {
            return null;
        }

        if ($recurrence === 'custom') {
            return json_encode([
                'type' => 'custom',
                'interval' => (int) ($request->validated('recurrence_interval') ?? 1),
                'unit' => $request->validated('recurrence_unit') ?? 'week',
                'days' => array_map('intval', $request->validated('recurrence_days') ?? []),
                'end_date' => $request->validated('recurrence_end_date'),
            ]);
        }

        return json_encode(['type' => $recurrence]);
    }

    private function eventPayload(Event $event): array
    {
        $recurrence = 'none';
        if ($event->recurrence_rule) {
            $rule = json_decode($event->recurrence_rule, true);
            $recurrence = ($rule['type'] ?? 'none') === 'custom' ? 'custom' : ($rule['type'] ?? 'none');
        }

        return [
            'id' => $event->id,
            'title' => $event->title,
            'location' => $event->location,
            'start_date' => $event->starts_at->toDateString(),
            'start_time' => $event->starts_at->format('H:i'),
            'end_date' => $event->ends_at?->toDateString(),
            'end_time' => $event->ends_at?->format('H:i'),
            'life_area_id' => $event->life_area_id,
            'recurrence' => $recurrence,
            'status' => $event->status->value,
        ];
    }
}
