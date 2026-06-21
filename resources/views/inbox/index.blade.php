@extends('layouts.app')

@section('title', 'Inbox — LifeOS')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold">Inbox</h1>
    <p class="text-sm text-zinc-500 mt-1">Capture → Clarify → Organize → Review → Execute</p>
</div>
<div class="card mb-4">
    <form method="POST" action="{{ route('inbox.quick') }}" class="flex gap-2">
        @csrf
        <input type="text" name="body" placeholder="Capture something..." class="input flex-1" required>
        <button type="submit" class="btn-primary">Capture</button>
    </form>
</div>
<div class="space-y-2">
    @forelse($items as $item)
        <div class="card flex items-center justify-between {{ $item->isProcessed() ? 'opacity-50' : '' }}">
            <p class="text-sm flex-1">{{ $item->body }}</p>
            <div class="flex gap-2 ml-4">
                @if(!$item->isProcessed())
                    <form method="POST" action="{{ route('inbox.convert-task', $item) }}">@csrf<button class="btn-secondary text-xs">→ Task</button></form>
                @endif
                <form method="POST" action="{{ route('inbox.destroy', $item) }}">@csrf @method('DELETE')<button class="text-xs text-red-500">Delete</button></form>
            </div>
        </div>
    @empty
        <div class="card text-center py-12 text-zinc-400">Inbox zero. Well done.</div>
    @endforelse
    {{ $items->links() }}
</div>
@endsection
