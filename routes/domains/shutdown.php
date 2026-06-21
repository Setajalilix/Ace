<?php

use App\Domains\Shutdown\Http\Controllers\ShutdownController;
use Illuminate\Support\Facades\Route;

Route::post('shutdown', [ShutdownController::class, 'update'])->name('shutdown.update');
