<?php

namespace Database\Seeders;

use App\Domains\LifeAreas\Actions\SeedDefaultLifeAreas;
use App\Database\Actions\SeedSampleContent;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LifeOSSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@lifeos.app'],
            ['name' => 'Demo User', 'password' => Hash::make('password')]
        );

        app(SeedDefaultLifeAreas::class)->execute($user);
        app(SeedSampleContent::class)->execute($user);
    }
}
