@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Admin</strong>. Today is {{ today()->format('F d, Y') }}.</p>

    <h2>Users</h2>
    <div class="grid">
        <div class="card"><div class="num">{{ $stats['total'] }}</div>Total users</div>
        @foreach (['Admin', 'Officer', 'Adviser', 'Student'] as $roleName)
            <div class="card"><div class="num">{{ $stats['roles'][$roleName] ?? 0 }}</div>{{ $roleName }}s</div>
        @endforeach
    </div>

    <h2>Needs attention</h2>
    <div class="grid">
        <div class="card">
            <div class="num">{{ $stats['pending'] }}</div>
            Pending approvals
            @if ($stats['pending'] > 0)
                <br><a href="{{ route('admin.users.index') }}">Review accounts</a>
            @endif
        </div>
    </div>

    <h2>School structure</h2>
    <div class="grid">
        <div class="card"><div class="num">{{ $stats['institutes'] }}</div><a href="{{ route('admin.institutes.index') }}">Institutes</a></div>
        <div class="card"><div class="num">{{ $stats['programs'] }}</div><a href="{{ route('admin.programs.index') }}">Programs</a></div>
    </div>

    @include('partials.weather')
@endsection
