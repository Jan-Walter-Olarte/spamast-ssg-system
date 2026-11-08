@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
    <p>You are logged in as <strong>Admin</strong>.</p>

    {{-- Placeholder numbers; real data comes in Stage B2 --}}
    <div class="grid">
        <div class="card"><div class="num">0</div>Total users</div>
        <div class="card"><div class="num">0</div>Active accounts</div>
        <div class="card"><div class="num">0</div>Institutes</div>
        <div class="card"><div class="num">0</div>Programs</div>
    </div>
@endsection
