@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Officer</strong>.</p>

    {{-- Placeholder numbers; real data comes in Stage B2 --}}
    <div class="grid">
        <div class="card"><div class="num">0</div>Upcoming events</div>
        <div class="card"><div class="num">0</div>Students</div>
        <div class="card"><div class="num">0</div>Attendance today</div>
        <div class="card"><div class="num">0</div>Unpaid fines</div>
    </div>
@endsection
