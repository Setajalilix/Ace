<?php

use App\Domains\Planner\Http\Controllers\PlannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('planner.today'));
Route::get('/planner/today', [PlannerController::class, 'today'])->name('planner.today');
