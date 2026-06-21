@props(['name' => 'due_date', 'label' => 'Date', 'value' => null, 'jalaliValue' => null, 'compact' => false])

@php
    $jalali = app(\App\Shared\Calendar\JalaliDateService::class);
    $gregorian = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
    $jYear = $jalaliValue ? explode('/', $jalaliValue)[0] ?? '' : ($value ? $jalali->format(\Carbon\Carbon::parse($value), 'Y') : '');
    $jMonth = $jalaliValue ? explode('/', $jalaliValue)[1] ?? '' : ($value ? $jalali->format(\Carbon\Carbon::parse($value), 'n') : '');
    $jDay = $jalaliValue ? explode('/', $jalaliValue)[2] ?? '' : ($value ? $jalali->format(\Carbon\Carbon::parse($value), 'j') : '');
    $months = $jalali->months();
    $years = $jalali->years();
@endphp

<div data-date-field="{{ $name }}"
     x-data="dateInput('{{ $name }}')"
     @ace:date-set.window="if ($event.detail.name === '{{ $name }}') setGregorian($event.detail.value)"
     class="space-y-2">
    @if($label)
        <label class="block text-sm font-medium text-[#6B5B4F]">{{ $label }}</label>
    @endif

    <div class="flex gap-1 p-1 bg-[#F3EDE4] rounded-xl w-fit">
        <button type="button" @click="tab='jalali'" :class="tab==='jalali' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="px-3 py-1 text-xs font-medium rounded-lg transition-all">Jalali</button>
        <button type="button" @click="tab='gregorian'" :class="tab==='gregorian' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="px-3 py-1 text-xs font-medium rounded-lg transition-all">Gregorian</button>
    </div>

    <div x-show="tab==='gregorian'" x-cloak>
        <input type="date" name="{{ $name }}" value="{{ old($name, $gregorian) }}" class="input" x-ref="gregorian">
    </div>

    <div x-show="tab==='jalali'" x-cloak class="flex flex-col gap-2 sm:grid sm:grid-cols-3 sm:gap-2.5">
        <select name="{{ $name }}_jalali_day" class="input {{ $compact ? 'text-sm py-2.5 min-h-[42px]' : '' }} w-full min-w-0" x-ref="jDay">
            <option value="">Day</option>
            @for($d = 1; $d <= 31; $d++)
                <option value="{{ $d }}" @selected($jDay == $d)>{{ $d }}</option>
            @endfor
        </select>
        <select name="{{ $name }}_jalali_month" class="input {{ $compact ? 'text-sm py-2.5 min-h-[42px]' : '' }} w-full min-w-0" x-ref="jMonth">
            <option value="">Month</option>
            @foreach($months as $num => $monthName)
                <option value="{{ $num }}" @selected($jMonth == $num)>{{ $monthName }}</option>
            @endforeach
        </select>
        <select name="{{ $name }}_jalali_year" class="input {{ $compact ? 'text-sm py-2.5 min-h-[42px]' : '' }} w-full min-w-0" x-ref="jYear">
            <option value="">Year</option>
            @foreach($years as $year)
                <option value="{{ $year }}" @selected($jYear == $year)>{{ $year }}</option>
            @endforeach
        </select>
        <input type="hidden" name="{{ $name }}_jalali" x-ref="jHidden" value="{{ old($name.'_jalali', $jalaliValue) }}">
    </div>
</div>
