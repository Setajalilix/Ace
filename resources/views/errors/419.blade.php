@extends('errors.layout')
@section('code', '419')
@section('emoji', '⏳')
@section('title', 'Session expired')
@section('message', 'Your session timed out. Refresh the page and sign in again if needed.')
@section('secondary')
    <a href="{{ route('login') }}" class="btn-secondary">Sign in</a>
@endsection
