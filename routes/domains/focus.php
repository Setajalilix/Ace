<?php

use App\Domains\Focus\Http\Controllers\FocusController;
use Illuminate\Support\Facades\Route;

Route::get('focus', [FocusController::class, 'index'])->name('focus.index');
