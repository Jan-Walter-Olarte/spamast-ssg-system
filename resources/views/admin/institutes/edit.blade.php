@extends('layouts.app')

@section('title', 'Edit Institute')

@section('content')
    <div class="page-head">
        <h1>Edit Institute</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('admin.institutes.form', ['action' => route('admin.institutes.update', $institute->id)])
    </div>
@endsection
