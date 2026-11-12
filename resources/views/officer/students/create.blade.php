@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
    <div class="page-head">
        <h1>Add Student</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('officer.students.form', ['action' => route('officer.students.store')])
    </div>
@endsection
