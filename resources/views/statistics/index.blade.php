@extends('layouts.app')
@section('title', 'Statistics — '.config('app.name'))
@section('content')
<div class="mb-6">
    <h1 class="page-title">Statistics</h1>
    <p class="text-sm text-[#A8958B] mt-1">Your progress at a glance</p>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card"><p class="text-xs text-[#A8958B]">Monthly Success</p><p class="text-2xl font-semibold mt-1 text-[#C47D5A]">{{ $daily_success_rate }}%</p></div>
    <div class="card"><p class="text-xs text-[#A8958B]">Completed Today</p><p class="text-2xl font-semibold mt-1 text-[#7BAE7F]">{{ $tasks_completed_today }}</p></div>
    <div class="card"><p class="text-xs text-[#A8958B]">Pending Tasks</p><p class="text-2xl font-semibold mt-1 text-[#6B9BD1]">{{ $tasks_pending }}</p></div>
    <div class="card"><p class="text-xs text-[#A8958B]">Active Goals</p><p class="text-2xl font-semibold mt-1 text-[#E6A23C]">{{ $active_goals }}</p></div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card">
        <h3 class="section-label mb-4">Life Area Balance (30 days)</h3>
        @forelse($life_area_balance as $area)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $area['color'] }}"></span>
                        {{ $area['name'] }}
                    </span>
                    <span class="text-[#A8958B]">{{ $area['pct'] }}%</span>
                </div>
                <div class="h-2 bg-[#F3EDE4] rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ $area['pct'] }}%; background: {{ $area['color'] }}"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-[#A8958B]">Complete tasks to see balance.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 class="section-label mb-4">Weekly Completion</h3>
        <div class="flex items-end gap-2 h-36 px-1">
            @php $barColors = ['#C47D5A', '#E8836B', '#E6A23C', '#7BAE7F', '#6B9BD1', '#C47D5A', '#A86545']; @endphp
            @foreach($weekly_completion as $day => $pct)
                @php $i = $loop->index; @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                    <span class="text-[10px] font-medium text-[#A8958B]">{{ $pct }}%</span>
                    <div class="w-full rounded-t-lg transition-all duration-300 min-h-[4px]"
                         style="height: {{ max($pct, 6) }}%; background: linear-gradient(to top, {{ $barColors[$i % count($barColors)] }}, {{ $barColors[$i % count($barColors)] }}99)"></div>
                    <span class="text-[10px] text-[#A8958B]">{{ $day }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h3 class="section-label mb-4">Habit Consistency</h3>
        @forelse($habit_consistency as $habit => $pct)
            <div class="py-2 border-b border-[#EDE5DA] last:border-0">
                <div class="flex justify-between text-sm mb-1"><span>{{ $habit }}</span><span class="text-[#7BAE7F] font-medium">{{ $pct }}%</span></div>
                <div class="h-1.5 bg-[#F3EDE4] rounded-full"><div class="h-full bg-[#7BAE7F] rounded-full" style="width: {{ $pct }}%"></div></div>
            </div>
        @empty
            <p class="text-sm text-[#A8958B]">No habits tracked.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 class="section-label mb-4">Time by Category</h3>
        @forelse($time_by_category as $cat => $count)
            <div class="flex justify-between text-sm py-2 border-b border-[#EDE5DA] last:border-0">
                <span>{{ $cat }}</span><span class="text-[#6B9BD1] font-medium">{{ $count }} blocks</span>
            </div>
        @empty
            <p class="text-sm text-[#A8958B]">No time blocks yet.</p>
        @endforelse
    </div>
</div>
@endsection
