@extends('layouts.app')
@section('title', 'Settings — '.config('app.name'))
@section('content')
<div class="mb-8">
    <h1 class="page-title">Settings</h1>
    <p class="text-sm text-[#A8958B] mt-1">Manage your profile and life areas</p>
</div>

<div class="space-y-8">
    {{-- Profile --}}
    <section class="card">
        <h2 class="text-lg font-semibold text-[#3D3229] mb-4">Profile</h2>
        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-[#6B5B4F] mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input @error('name') border-[#C0392B] @enderror" required>
                @error('name')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-[#6B5B4F] mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input @error('email') border-[#C0392B] @enderror" required>
                @error('email')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary">Save profile</button>
        </form>
    </section>

    {{-- Password --}}
    <section class="card">
        <h2 class="text-lg font-semibold text-[#3D3229] mb-4">Password</h2>
        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="block text-sm font-medium text-[#6B5B4F] mb-1">Current password</label>
                <input type="password" id="current_password" name="current_password" class="input @error('current_password') border-[#C0392B] @enderror" required autocomplete="current-password">
                @error('current_password')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-[#6B5B4F] mb-1">New password</label>
                <input type="password" id="password" name="password" class="input @error('password') border-[#C0392B] @enderror" required autocomplete="new-password">
                @error('password')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#6B5B4F] mb-1">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn-primary">Update password</button>
        </form>
    </section>

    {{-- Life areas --}}
    <section id="life-areas" class="scroll-mt-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-[#3D3229]">Life areas</h2>
                <p class="text-sm text-[#A8958B] mt-0.5">Organize tasks, habits, and goals by area</p>
            </div>
        </div>

        <div class="card mb-6">
            <h3 class="font-medium text-[#3D3229] mb-3">Add life area</h3>
            <form method="POST" action="{{ route('life-areas.store') }}" class="space-y-4">
                @csrf
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input @error('name') border-[#C0392B] @enderror" placeholder="e.g. Creative, Finance..." required>
                        @error('name')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Color</label>
                        <input type="color" name="color" value="{{ old('color', '#C47D5A') }}" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                        @error('color')<p class="text-xs text-[#C0392B] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> Add</button>
                </div>
            </form>
        </div>

        <div class="space-y-3">
            @foreach($lifeAreas as $area)
                <div class="card" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: {{ $area->color }}20">
                                <span class="w-4 h-4 rounded-full" style="background-color: {{ $area->color }}"></span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-[#3D3229] truncate">{{ $area->name }}</h3>
                                <p class="text-xs text-[#A8958B]">
                                    {{ $area->tasks_count }} tasks · {{ $area->habits_count }} habits · {{ $area->goals_count }} goals
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <button type="button" @click="editing = true" class="btn-ghost text-xs px-2 py-1">Edit</button>
                            @if($lifeAreas->count() > 1)
                                <form method="POST" action="{{ route('life-areas.destroy', $area) }}" onsubmit="return confirm('Delete this life area? Items linked to it will lose their area.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-xs px-2 py-1 text-[#C0392B]">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <form x-show="editing" x-cloak method="POST" action="{{ route('life-areas.update', $area) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-wrap gap-3 items-end">
                            <div class="flex-1 min-w-[140px]">
                                <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Name</label>
                                <input type="text" name="name" value="{{ $area->name }}" class="input" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#6B5B4F] mb-1">Color</label>
                                <input type="color" name="color" value="{{ $area->color }}" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="btn-primary text-sm">Save</button>
                                <button type="button" @click="editing = false" class="btn-secondary text-sm">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
