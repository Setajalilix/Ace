@extends('layouts.app')
@section('title', 'New Task — '.config('app.name'))
@section('content')
<h1 class="page-title mb-6">New Task</h1>
<div class="card">
    <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
        @csrf
        <x-task-form-fields />
        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary">Create task</button>
            <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
