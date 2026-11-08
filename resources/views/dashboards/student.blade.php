@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Student</strong>.</p>

    {{-- Placeholder numbers; real data comes in Stage B2 --}}
    <div class="grid">
        <div class="card"><div class="num">0</div>Upcoming events</div>
        <div class="card"><div class="num">0</div>My attendance</div>
        <div class="card"><div class="num">0</div>My fines</div>
        <div class="card"><div class="num">0</div>Announcements</div>
    </div>
@endsection
