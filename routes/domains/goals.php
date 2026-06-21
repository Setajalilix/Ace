<?php

use App\Domains\Goals\Http\Controllers\GoalController;
use Illuminate\Support\Facades\Route;

Route::resource('goals', GoalController::class);
