@extends('layouts.app')

@section('title', 'Add Institute')

@section('content')
    <div class="page-head">
        <h1>Add Institute</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('admin.institutes.form', ['action' => route('admin.institutes.store')])
    </div>
@endsection
