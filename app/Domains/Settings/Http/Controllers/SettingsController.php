<?php

namespace App\Domains\Settings\Http\Controllers;

use App\Domains\Settings\Http\Requests\UpdatePasswordRequest;
use App\Domains\Settings\Http\Requests\UpdateProfileRequest;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lifeAreas = $user->lifeAreas()
            ->withCount(['tasks', 'habits', 'goals'])
            ->orderBy('sort_order')
            ->get();

        return view('settings.index', compact('user', 'lifeAreas'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return redirect()->route('settings.index')->with('success', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->route('settings.index')->with('success', 'Password updated.');
    }
}
