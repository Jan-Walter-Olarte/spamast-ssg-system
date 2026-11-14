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

    <div class="chart-grid">
        <div>
            @include('partials.bar-chart', [
                'title' => 'Students who attended the latest events',
                'items' => $charts['attendance'],
                'money' => false,
                'empty' => 'No events yet.',
            ])
        </div>
        <div>
            @include('partials.bar-chart', [
                'title' => 'Unpaid fines by program',
                'items' => $charts['fines'],
                'money' => true,
                'empty' => 'There are no unpaid fines.',
            ])
        </div>
    </div>

    @include('partials.weather')

    @include('partials.latest-announcements')
@endsection
