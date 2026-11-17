@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p class="page-intro">You are logged in as <strong>Student</strong>. Today is {{ today()->format('l, F d, Y') }}. Use a quick action to get started.</p>

    @include('partials.quick-actions')

    <h2>Summary</h2>
    <div class="grid">
        <div class="card"><div class="num">{{ $stats['events'] }}</div><a href="{{ route('events.index') }}">Upcoming events</a></div>
        <div class="card"><div class="num">{{ $stats['attendance'] }}</div><a href="{{ route('student.my-attendance') }}">My attendance records</a></div>
        <div class="card"><div class="num">&#8369;</div><a href="{{ route('student.my-fines') }}">View my fines</a></div>
    </div>

    <h2>How to check in</h2>
    <div class="panel panel-wide">
        <ol class="steps-list">
            <li>Open <a href="{{ route('events.index') }}">Events &amp; Check-in</a> and choose the event you are attending.</li>
            <li>Ask for the <strong>check-in code</strong> shown at the venue.</li>
            <li>Type the code next to <strong>Check in</strong> and press the button. Do the same with <strong>Check out</strong> when you leave.</li>
        </ol>
        <p class="hint">The buttons only appear while check-in or check-out is open. Your records are under <a href="{{ route('student.my-attendance') }}">My Attendance</a>.</p>
    </div>

    @include('partials.weather')

    @include('partials.latest-announcements')
@endsection
