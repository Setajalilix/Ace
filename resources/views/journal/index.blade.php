@extends('layouts.app')
@section('title', 'Journal — '.config('app.name'))
@section('content')
<div class="mb-8" x-data="{ tab: '{{ now()->hour < 17 ? 'morning' : 'evening' }}' }">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="page-title">Journal</h1>
            <p class="text-sm text-[#A8958B] mt-1">{{ $today->format('l, M j, Y') }} · {{ $jalaliLabel }}</p>
        </div>
        <div class="flex gap-1 p-1 bg-[#F3EDE4] rounded-xl w-fit">
            <button type="button" @click="tab='morning'" :class="tab==='morning' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">☀️ Morning</button>
            <button type="button" @click="tab='evening'" :class="tab==='evening' ? 'bg-white shadow-sm text-[#3D3229]' : 'text-[#A8958B]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">🌙 Evening</button>
        </div>
    </div>

    <div class="grid lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3">
            {{-- Morning --}}
            <div x-show="tab==='morning'" x-cloak class="card space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-[#EDE5DA]">
                    <div class="w-10 h-10 rounded-xl bg-[#FEF3E0] flex items-center justify-center text-lg">☀️</div>
                    <div>
                        <h2 class="font-medium text-[#3D3229]">Morning intentions</h2>
                        <p class="text-xs text-[#A8958B]">Set the tone before the day runs you</p>
                    </div>
                    @if($morning)
                        <span class="ml-auto text-xs font-medium text-[#7BAE7F] bg-[#E8F5E9] px-2 py-1 rounded-full">Saved</span>
                    @endif
                </div>
                <form x-data="journalForm('morning', '{{ $today->toDateString() }}')" @submit="submit($event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today->toDateString() }}">
                    <input type="hidden" name="type" value="morning">
                    @foreach([
                        ['key' => 'important_today', 'label' => 'Most important today', 'placeholder' => 'If I only finish one thing, it should be…', 'icon' => '🎯'],
                        ['key' => 'distractions', 'label' => 'What could distract me?', 'placeholder' => 'Meetings, notifications, procrastination…', 'icon' => '⚡'],
                        ['key' => 'desired_feeling', 'label' => 'How do I want to feel?', 'placeholder' => 'Calm, focused, energized…', 'icon' => '💫'],
                    ] as $field)
                        <div>
                            <label class="flex items-center gap-2 text-sm font-medium text-[#6B5B4F] mb-1.5">
                                <span>{{ $field['icon'] }}</span> {{ $field['label'] }}
                            </label>
                            <textarea name="content[{{ $field['key'] }}]" class="input min-h-[72px]" placeholder="{{ $field['placeholder'] }}">{{ $morning?->content[$field['key']] ?? '' }}</textarea>
                        </div>
                    @endforeach
                    <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="saving">
                        <span x-show="!saving && !saved">Save morning entry</span>
                        <span x-show="saving" x-cloak>Saving…</span>
                        <span x-show="saved && !saving" x-cloak>Saved ✓</span>
                    </button>
                </form>
            </div>

            {{-- Evening --}}
            <div x-show="tab==='evening'" x-cloak class="card space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-[#EDE5DA]">
                    <div class="w-10 h-10 rounded-xl bg-[#EDE5F5] flex items-center justify-center text-lg">🌙</div>
                    <div>
                        <h2 class="font-medium text-[#3D3229]">Evening reflection</h2>
                        <p class="text-xs text-[#A8958B]">Close the loop — learn and let go</p>
                    </div>
                    @if($evening)
                        <span class="ml-auto text-xs font-medium text-[#7BAE7F] bg-[#E8F5E9] px-2 py-1 rounded-full">Saved</span>
                    @endif
                </div>
                <form x-data="journalForm('evening', '{{ $today->toDateString() }}')" @submit="submit($event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today->toDateString() }}">
                    <input type="hidden" name="type" value="evening">
                    @foreach([
                        ['key' => 'went_well', 'label' => 'What went well?', 'placeholder' => 'Wins, moments of flow, kindness…', 'icon' => '✨'],
                        ['key' => 'did_not_go_well', 'label' => 'What didn\'t go well?', 'placeholder' => 'Honest, not harsh — just data', 'icon' => '🌧'],
                        ['key' => 'improve_tomorrow', 'label' => 'Improve tomorrow', 'placeholder' => 'One small adjustment…', 'icon' => '🌱'],
                    ] as $field)
                        <div>
                            <label class="flex items-center gap-2 text-sm font-medium text-[#6B5B4F] mb-1.5">
                                <span>{{ $field['icon'] }}</span> {{ $field['label'] }}
                            </label>
                            <textarea name="content[{{ $field['key'] }}]" class="input min-h-[72px]" placeholder="{{ $field['placeholder'] }}">{{ $evening?->content[$field['key']] ?? '' }}</textarea>
                        </div>
                    @endforeach
                    <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="saving">
                        <span x-show="!saving && !saved">Save evening entry</span>
                        <span x-show="saving" x-cloak>Saving…</span>
                        <span x-show="saved && !saving" x-cloak>Saved ✓</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="card-flat sticky top-6">
                <h2 class="section-label mb-4">Recent entries</h2>
                @forelse($entries as $entry)
                    <div class="py-3 border-b border-[#EDE5DA] last:border-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold uppercase tracking-wide {{ $entry->type->value === 'morning' ? 'text-[#E6A23C]' : 'text-[#6B9BD1]' }}">
                                {{ $entry->type->value === 'morning' ? '☀️ Morning' : '🌙 Evening' }}
                            </span>
                            <span class="text-xs text-[#A8958B]"><x-jalali-date :date="$entry->date" /></span>
                        </div>
                        @foreach($entry->content as $key => $value)
                            @if($value)
                                <p class="text-sm text-[#6B5B4F] line-clamp-2 mb-1">
                                    <span class="text-[#A8958B] capitalize">{{ str_replace('_', ' ', $key) }}:</span> {{ $value }}
                                </p>
                            @endif
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-[#A8958B] text-center py-8">Your journal history will appear here.</p>
                @endforelse
                <div class="mt-3">{{ $entries->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
