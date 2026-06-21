<?php

use App\Domains\Tasks\Http\Controllers\TaskBoardController;
use App\Domains\Tasks\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('board', [TaskBoardController::class, 'kanban'])->name('board.index');
Route::patch('board/{task}', [TaskBoardController::class, 'updateKanban'])->name('board.update');

Route::resource('tasks', TaskController::class)->except(['show']);
Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::post('tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
Route::post('tasks/{task}/time-block', [TaskController::class, 'toTimeBlock'])->name('tasks.time-block');
