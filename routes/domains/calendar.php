<?php

use App\Domains\Calendar\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('calendar/date-parts', [CalendarController::class, 'dateParts'])->name('calendar.date-parts');
