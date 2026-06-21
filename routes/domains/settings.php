<?php

use App\Domains\Settings\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
