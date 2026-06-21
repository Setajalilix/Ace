<?php

use App\Domains\TimeBlocks\Http\Controllers\TimeBlockController;
use Illuminate\Support\Facades\Route;

Route::post('time-blocks', [TimeBlockController::class, 'store'])->name('time-blocks.store');
Route::post('time-blocks/{timeBlock}/start', [TimeBlockController::class, 'start'])->name('time-blocks.start');
Route::post('time-blocks/{timeBlock}/complete', [TimeBlockController::class, 'complete'])->name('time-blocks.complete');
Route::post('time-blocks/{timeBlock}/reschedule', [TimeBlockController::class, 'reschedule'])->name('time-blocks.reschedule');
Route::delete('time-blocks/{timeBlock}', [TimeBlockController::class, 'destroy'])->name('time-blocks.destroy');
