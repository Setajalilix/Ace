<?php

use App\Domains\LifeAreas\Http\Controllers\LifeAreaController;
use Illuminate\Support\Facades\Route;

Route::resource('life-areas', LifeAreaController::class)->except(['show']);
