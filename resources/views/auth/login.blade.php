@extends('layouts.app')
@section('title', 'Sign in — '.config('app.name'))
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 bg-[#FAF7F2]">
    <div class="w-full max-w-sm fade-in">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-[#C47D5A] flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-lg">A</span>
            </div>
            <h1 class="text-2xl font-semibold text-[#3D3229]">Welcome back</h1>
            <p class="text-sm text-[#A8958B] mt-2">Sign in to {{ config('app.name') }}</p>
        </div>
        <div class="card">
            <form method="POST" action="{{ route('login', [], false) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
                    @error('email')<p class="text-[#E05D44] text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Password</label>
                    <input type="password" name="password" class="input" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-[#6B5B4F] cursor-pointer">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#D4C4B5] text-[#C47D5A] focus:ring-[#C47D5A]/30" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <button type="submit" class="btn-primary w-full">Sign in</button>
            </form>
            <p class="text-sm text-[#A8958B] text-center mt-4">
                No account? <a href="{{ route('register') }}" class="text-[#C47D5A] font-medium hover:underline">Create one</a>
            </p>
        </div>
    </div>
</div>
@endsection
