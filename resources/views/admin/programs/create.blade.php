@extends('layouts.app')

@section('title', 'Add Program')

@section('content')
    <div class="page-head">
        <h1>Add Program</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('admin.programs.form', ['action' => route('admin.programs.store')])
    </div>
@endsection
