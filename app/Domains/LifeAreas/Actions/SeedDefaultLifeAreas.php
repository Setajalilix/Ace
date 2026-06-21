<?php

namespace App\Domains\LifeAreas\Actions;

use App\Domains\LifeAreas\Models\LifeArea;
use App\Domains\Auth\Models\User;

class SeedDefaultLifeAreas
{
    public function execute(User $user): void
    {
        $areas = [
            ['name' => 'Work', 'slug' => 'work', 'color' => '#6366f1', 'sort_order' => 1],
            ['name' => 'Personal', 'slug' => 'personal', 'color' => '#8b5cf6', 'sort_order' => 2],
            ['name' => 'Health', 'slug' => 'health', 'color' => '#10b981', 'sort_order' => 3],
            ['name' => 'Family', 'slug' => 'family', 'color' => '#f59e0b', 'sort_order' => 4],
        ];

        foreach ($areas as $area) {
            LifeArea::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $area['slug']],
                array_merge($area, ['user_id' => $user->id])
            );
        }
    }
}
