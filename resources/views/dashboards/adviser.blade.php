@extends('layouts.app')

@section('title', 'Adviser Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Adviser</strong>.</p>

    {{-- Placeholder numbers; real data comes in Stage B2 --}}
    <div class="grid">
        <div class="card"><div class="num">0</div>Upcoming events</div>
        <div class="card"><div class="num">0</div>Attendance records</div>
        <div class="card"><div class="num">0</div>Fines issued</div>
        <div class="card"><div class="num">0</div>Reports</div>
    </div>
@endsection
