<?php

namespace App\Domains\Events\Services;

use App\Domains\Events\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EventRecurrenceService
{
    public function label(?string $recurrenceRule): string
    {
        if (! $recurrenceRule || $this->recurrenceType($recurrenceRule) === 'none') {
            return 'Does not repeat';
        }

        $data = json_decode($recurrenceRule, true) ?: [];
        $type = $data['type'] ?? 'none';

        return match ($type) {
            'daily' => 'Every day',
            'weekly' => 'Every week',
            'weekdays' => 'Weekdays (Sat–Wed)',
            'monthly' => 'Every month',
            'custom' => $this->customLabel($data),
            default => 'Does not repeat',
        };
    }

    public function occursOn(Event $event, Carbon $date): bool
    {
        $day = $this->calendarDay($date);

        if ($this->isNonRepeating($event)) {
            return $this->onSameCalendarDay($event->starts_at, $day);
        }

        $data = json_decode($event->recurrence_rule, true) ?: [];
        $type = $data['type'] ?? 'none';
        $start = $this->calendarDay($event->starts_at);

        if ($day->lt($start)) {
            return false;
        }

        if (! empty($data['end_date']) && $day->gt(Carbon::parse($data['end_date'], config('app.timezone'))->startOfDay())) {
            return false;
        }

        return match ($type) {
            'daily' => true,
            'weekdays' => $this->isIranianWeekday($day),
            'weekly' => $day->dayOfWeek === $start->dayOfWeek,
            'monthly' => $day->day === $start->day,
            'custom' => $this->matchesCustom($data, $day, $start),
            default => $this->onSameCalendarDay($event->starts_at, $day),
        };
    }

    public function forDate(Collection $events, Carbon $date): Collection
    {
        $day = $this->calendarDay($date);

        return $events->filter(fn (Event $event) => $this->occursOn($event, $day));
    }

    private function isNonRepeating(Event $event): bool
    {
        if (! $event->recurrence_rule) {
            return true;
        }

        return $this->recurrenceType($event->recurrence_rule) === 'none';
    }

    private function recurrenceType(?string $recurrenceRule): string
    {
        if (! $recurrenceRule) {
            return 'none';
        }

        $data = json_decode($recurrenceRule, true) ?: [];

        return $data['type'] ?? 'none';
    }

    private function calendarDay(Carbon $date): Carbon
    {
        return $date->copy()->timezone(config('app.timezone'))->startOfDay();
    }

    private function onSameCalendarDay(Carbon $instant, Carbon $day): bool
    {
        return $this->calendarDay($instant)->toDateString() === $day->toDateString();
    }

    private function isIranianWeekday(Carbon $day): bool
    {
        return in_array($day->dayOfWeek, [
            Carbon::SATURDAY,
            Carbon::SUNDAY,
            Carbon::MONDAY,
            Carbon::TUESDAY,
            Carbon::WEDNESDAY,
        ], true);
    }

    private function customLabel(array $data): string
    {
        $interval = $data['interval'] ?? 1;
        $unit = $data['unit'] ?? 'week';

        if ($unit === 'week' && ! empty($data['days'])) {
            $days = collect($data['days'])->map(fn ($d) => Carbon::now()->startOfWeek()->addDays($d)->format('D'))->join(', ');

            return "Every {$interval} week(s) on {$days}";
        }

        return "Every {$interval} {$unit}(s)";
    }

    private function matchesCustom(array $data, Carbon $date, Carbon $start): bool
    {
        $interval = max(1, (int) ($data['interval'] ?? 1));
        $unit = $data['unit'] ?? 'week';

        if ($unit === 'day') {
            return $start->diffInDays($date) % $interval === 0;
        }

        if ($unit === 'week') {
            $weeks = intdiv($start->diffInDays($date), 7);

            if ($weeks % $interval !== 0) {
                return false;
            }

            $days = $data['days'] ?? [$start->dayOfWeek];

            return in_array($date->dayOfWeek, $days);
        }

        if ($unit === 'month') {
            $months = $start->diffInMonths($date);

            return $months % $interval === 0 && $date->day === $start->day;
        }

        return false;
    }
}
