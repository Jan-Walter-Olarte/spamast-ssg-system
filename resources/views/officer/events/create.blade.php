@extends('layouts.app')

@section('title', 'Add Event')

@section('content')
    <div class="page-head">
        <h1>Add Event</h1>
    </div>

    <div class="panel panel-wide">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('officer.events.form', ['action' => route('officer.events.store')])
    </div>
@endsection
