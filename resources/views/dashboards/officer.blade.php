@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p class="page-intro">You are logged in as <strong>Officer</strong>. Today is {{ today()->format('l, F d, Y') }}. Start with a quick action, or check the summary and charts below.</p>

    @include('partials.quick-actions')

    <h2>Summary</h2>
    <div class="grid">
        <div class="card"><div class="num">{{ $stats['students'] }}</div><a href="{{ route('officer.students.index') }}">Students</a></div>
        <div class="card"><div class="num">{{ $stats['events'] }}</div><a href="{{ route('events.index') }}">Upcoming events</a></div>
        <div class="card"><div class="num">{{ $stats['unpaid'] }}</div>Unpaid fines</div>
        <div class="card"><div class="num">{{ $stats['announcements'] }}</div><a href="{{ route('announcements.index') }}">Announcements</a></div>
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
