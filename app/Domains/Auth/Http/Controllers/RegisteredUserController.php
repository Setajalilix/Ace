<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Domains\LifeAreas\Actions\SeedDefaultLifeAreas;
use App\Database\Actions\SeedSampleContent;
use App\Shared\Http\Controllers\Controller;
use App\Domains\Auth\Http\Requests\RegisterRequest;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, SeedDefaultLifeAreas $seedLifeAreas, SeedSampleContent $seedSample)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $seedLifeAreas->execute($user);
        $seedSample->execute($user);

        Auth::login($user);

        return redirect()->route('planner.today');
    }
}
