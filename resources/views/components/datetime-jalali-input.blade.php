@props(['name' => 'start_date', 'timeName' => 'start_time', 'dateLabel' => 'Date', 'timeLabel' => 'Time', 'dateValue' => null, 'timeValue' => null])

<div class="space-y-3">
    <x-date-input :name="$name" :label="$dateLabel" :value="$dateValue" />
    <div>
        <label class="block text-sm font-medium text-[#6B5B4F] mb-1">{{ $timeLabel }}</label>
        <input type="time" name="{{ $timeName }}" value="{{ old($timeName, $timeValue) }}" class="input" required>
    </div>
</div>
