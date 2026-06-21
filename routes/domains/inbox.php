<?php

use App\Domains\Inbox\Http\Controllers\InboxController;
use Illuminate\Support\Facades\Route;

Route::get('inbox', [InboxController::class, 'index'])->name('inbox.index');
Route::post('inbox/quick', [InboxController::class, 'quickCapture'])->name('inbox.quick');
Route::delete('inbox/{inboxItem}', [InboxController::class, 'destroy'])->name('inbox.destroy');
Route::post('inbox/{inboxItem}/convert-task', [InboxController::class, 'convertTask'])->name('inbox.convert-task');
