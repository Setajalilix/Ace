<?php

use App\Domains\Notes\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::get('notes/{note}', [NoteController::class, 'preview'])->name('notes.show');
