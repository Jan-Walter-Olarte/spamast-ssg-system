@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Officer</strong>. Today is {{ today()->format('F d, Y') }}.</p>

    <div class="grid">
        <div class="card"><div class="num">{{ $stats['students'] }}</div><a href="{{ route('officer.students.index') }}">Students</a></div>
        <div class="card"><div class="num">{{ $stats['events'] }}</div><a href="{{ route('events.index') }}">Upcoming events</a></div>
        <div class="card"><div class="num">{{ $stats['unpaid'] }}</div>Unpaid fines</div>
        <div class="card"><div class="num">{{ $stats['announcements'] }}</div><a href="{{ route('announcements.index') }}">Announcements</a></div>
    </div>

    @include('partials.latest-announcements')
@endsection
