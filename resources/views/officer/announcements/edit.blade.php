@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
    <div class="page-head">
        <h1>Edit Announcement</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('officer.announcements.form', ['action' => route('officer.announcements.update', $announcement)])
    </div>
@endsection
