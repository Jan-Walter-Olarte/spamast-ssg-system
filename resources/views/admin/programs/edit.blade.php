@extends('layouts.app')

@section('title', 'Edit Program')

@section('content')
    <div class="page-head">
        <h1>Edit Program</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('admin.programs.form', ['action' => route('admin.programs.update', $program->id)])
    </div>
@endsection
