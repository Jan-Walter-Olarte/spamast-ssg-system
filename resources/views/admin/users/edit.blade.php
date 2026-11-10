@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="page-head">
        <h1>Edit User</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('admin.users.form', ['action' => route('admin.users.update', $user)])
    </div>
@endsection