@props(['tasks', 'compact' => false])

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3 {{ $compact ? '' : 'min-h-[280px]' }}"
     data-kanban-board
     x-data="kanbanBoard()">
    @foreach(\App\Domains\Tasks\Enums\KanbanColumn::ordered() as $column)
        <div class="min-w-0 flex flex-col">
            <div class="flex items-center gap-2 mb-2 px-1">
                <span class="section-label truncate">{{ $column->label() }}</span>
                <span class="text-xs text-[#A8958B]">{{ ($tasks->get($column->value, collect()))->count() }}</span>
            </div>
            <div class="flex-1 space-y-2 p-2 bg-[#F3EDE4]/60 rounded-2xl border border-[#EDE5DA]/80 min-h-[120px]"
                 @dragover.prevent @drop="drop($event, '{{ $column->value }}')">
                @foreach($tasks->get($column->value, collect()) as $task)
                    <div class="card-flat p-2.5 hover:shadow-md transition-shadow {{ $compact ? 'text-xs' : '' }}">
                        <div class="flex items-start gap-2">
                            @if(!$task->isCompleted())
                                <form method="POST" action="{{ route('tasks.complete', $task) }}" class="flex-shrink-0 mt-0.5">@csrf
                                    <button type="submit" class="w-4 h-4 rounded-full border-2 border-[#D4C4B5] hover:border-[#7BAE7F] hover:bg-[#7BAE7F]/20"></button>
                                </form>
                            @else
                                <span class="w-4 h-4 rounded-full bg-[#7BAE7F] flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                                </span>
                            @endif
                            <div class="flex-1 min-w-0 cursor-grab active:cursor-grabbing" draggable="true" @dragstart="dragStart({{ $task->id }})">
                                <p class="text-sm font-medium text-[#3D3229] mb-1 line-clamp-2">{{ $task->title }}</p>
                                <div class="flex flex-wrap items-center gap-1">
                                    <x-priority-badge :priority="$task->priority" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

@once
<script>
function kanbanBoard() {
    return {
        draggedId: null,
        init() {
            const refresh = () => {
                if (document.querySelector('[data-kanban-board]')) {
                    window.location.reload();
                }
            };
            window.addEventListener('ace:task-saved', refresh);
            window.addEventListener('ace:task-completed', refresh);
        },
        dragStart(id) { this.draggedId = id; },
        async drop(e, column) {
            if (!this.draggedId) return;
            await fetch(`/board/${this.draggedId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ kanban_column: column }),
            });
            location.reload();
        },
    };
}
</script>
@endonce
