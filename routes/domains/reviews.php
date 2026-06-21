<?php

use App\Domains\Reviews\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('reviews/weekly', [ReviewController::class, 'weekly'])->name('reviews.weekly');
Route::post('reviews/weekly', [ReviewController::class, 'saveWeekly'])->name('reviews.weekly.save');
Route::get('reviews/monthly', [ReviewController::class, 'monthly'])->name('reviews.monthly');
Route::post('reviews/monthly', [ReviewController::class, 'saveMonthly'])->name('reviews.monthly.save');
