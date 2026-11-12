@extends('layouts.app')

@section('title', 'Post Announcement')

@section('content')
    <div class="page-head">
        <h1>Post Announcement</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('officer.announcements.form', ['action' => route('officer.announcements.store')])
    </div>
@endsection
