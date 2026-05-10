<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('habits', HabitController::class);

Route::post('/habits/{habit}/toggle',
    [HabitLogController::class, 'toggle'])
    ->name('habits.toggle');

Route::post('/habits/{habit}/timer',
    [HabitLogController::class, 'saveTimer'])
    ->name('habits.timer');
