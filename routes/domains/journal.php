<?php

use App\Domains\Journal\Http\Controllers\JournalController;
use Illuminate\Support\Facades\Route;

Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
Route::post('journal', [JournalController::class, 'store'])->name('journal.store');
