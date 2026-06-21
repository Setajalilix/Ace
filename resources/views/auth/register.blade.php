@extends('layouts.app')
@section('title', 'Register — '.config('app.name'))
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 bg-[#FAF7F2]">
    <div class="w-full max-w-sm fade-in">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-[#C47D5A] flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-lg">A</span>
            </div>
            <h1 class="text-2xl font-semibold text-[#3D3229]">Start with {{ config('app.name') }}</h1>
            <p class="text-sm text-[#A8958B] mt-2">Sample tasks & habits included</p>
        </div>
        <div class="card">
            <form method="POST" action="{{ route('register', [], false) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input" required autofocus>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" required>
                    @error('email')<p class="text-[#E05D44] text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Password</label>
                    <input type="password" name="password" class="input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#6B5B4F] mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input" required>
                </div>
                <button type="submit" class="btn-primary w-full">Create account</button>
            </form>
        </div>
    </div>
</div>
@endsection
