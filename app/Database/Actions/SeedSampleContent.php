<?php

namespace App\Database\Actions;

use App\Domains\Goals\Enums\GoalType;
use App\Domains\Tasks\Enums\KanbanColumn;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\Goals\Models\Goal;
use App\Domains\Habits\Models\Habit;
use App\Domains\Tasks\Models\Task;
use App\Domains\Auth\Models\User;

class SeedSampleContent
{
    public function execute(User $user): void
    {
        $work = $user->lifeAreas()->where('slug', 'work')->first() ?? $user->lifeAreas()->first();
        $health = $user->lifeAreas()->where('slug', 'health')->first() ?? $user->lifeAreas()->first();

        if (! $work || ! $health) {
            return;
        }

        $goal = Goal::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Build a balanced life'],
            [
                'life_area_id' => $work->id,
                'why' => 'Live with clarity and intention.',
                'type' => GoalType::Quarterly,
                'target_date' => now()->addMonths(3),
                'progress' => 15,
            ]
        );

        $today = today();

        foreach ([
            ['title' => 'Review today\'s top 3 priorities', 'priority' => TaskPriority::P1],
            ['title' => 'Process inbox to zero', 'priority' => TaskPriority::P2],
            ['title' => 'Write evening reflection', 'priority' => TaskPriority::P2],
            ['title' => 'Read for 20 minutes', 'priority' => TaskPriority::P3],
        ] as $data) {
            Task::firstOrCreate(
                ['user_id' => $user->id, 'title' => $data['title'], 'due_date' => $today],
                [
                    'life_area_id' => $work->id,
                    'priority' => $data['priority'],
                    'status' => TaskStatus::Pending,
                    'kanban_column' => KanbanColumn::Next,
                ]
            );
        }

        foreach ([
            ['title' => 'Morning meditation', 'color' => '#C47D5A', 'type' => 'checkbox'],
            ['title' => 'Drink 8 glasses of water', 'color' => '#6B9BD1', 'type' => 'counter', 'target_count' => 8],
            ['title' => 'Read 30 pages', 'color' => '#7BAE7F', 'type' => 'counter', 'target_count' => 30],
            ['title' => 'Deep work timer', 'color' => '#E8A838', 'type' => 'timer', 'target_minutes' => 25, 'daily_increment' => 5],
        ] as $data) {
            Habit::firstOrCreate(
                ['user_id' => $user->id, 'title' => $data['title']],
                array_merge($data, [
                    'life_area_id' => $health->id,
                    'repeat_every' => 1,
                    'start_date' => $today,
                ])
            );
        }
    }
}
