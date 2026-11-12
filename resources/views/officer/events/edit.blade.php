@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
    <div class="page-head">
        <h1>Edit Event</h1>
    </div>

    <div class="panel panel-wide">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('officer.events.form', ['action' => route('officer.events.update', $event)])
    </div>
@endsection
