@extends('layouts.app')

@section('title', 'Adviser Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Adviser</strong>. Today is {{ today()->format('F d, Y') }}.</p>

    <div class="grid">
        <div class="card"><div class="num">{{ $stats['events'] }}</div><a href="{{ route('events.index') }}">Upcoming events</a></div>
        <div class="card"><div class="num">{{ $stats['attendance'] }}</div>Attendance records</div>
        <div class="card"><div class="num">{{ $stats['unpaid'] }}</div>Unpaid fines</div>
    </div>
@endsection
