<?php

use App\Domains\Statistics\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
