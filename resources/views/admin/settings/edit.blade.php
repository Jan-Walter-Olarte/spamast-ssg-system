@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="page-head">
        <h1>Settings</h1>
    </div>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <label for="school_name">School or organization name</label>
            <input type="text" id="school_name" name="school_name" maxlength="120"
                   value="{{ old('school_name', $schoolName) }}" required>
            <p class="hint">Shown in the footer of the home page and at the top of printed reports.</p>

            <label for="default_fine">Default fine per missed scan (&#8369;)</label>
            <input type="number" id="default_fine" name="default_fine" min="0" step="0.01"
                   value="{{ old('default_fine', $defaultFine) }}" required>
            <p class="hint">Pre-filled when an Officer creates a <strong>new</strong> event. Events that already exist keep their own fine, and the Officer can still change it for each event.</p>

            <button type="submit" class="btn" style="margin-top: 20px;">Save settings</button>
        </form>
    </div>
@endsection
