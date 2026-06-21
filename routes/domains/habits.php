<?php

use App\Domains\Habits\Http\Controllers\HabitController;
use App\Domains\Habits\Http\Controllers\HabitLogController;
use Illuminate\Support\Facades\Route;

Route::resource('habits', HabitController::class);
Route::post('habits/{habit}/toggle', [HabitLogController::class, 'toggle'])->name('habits.toggle');
Route::post('habits/{habit}/timer', [HabitLogController::class, 'saveTimer'])->name('habits.timer');
Route::post('habits/{habit}/counter', [HabitLogController::class, 'saveCounter'])->name('habits.counter');
Route::post('habits/{habit}/counter/increment', [HabitLogController::class, 'incrementCounter'])->name('habits.counter.increment');
