@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
    <div class="page-head">
        <h1>{{ $announcement->title }}</h1>
        <div class="actions">
            <a href="{{ route('announcements.index') }}" class="btn btn-light">Back to announcements</a>

            @can('manage-ssg')
                <a href="{{ route('officer.announcements.edit', $announcement) }}" class="btn">Edit</a>
            @endcan
        </div>
    </div>

    <div class="panel panel-wide">
        <p style="color: var(--muted);">
            Posted by {{ $announcement->user->full_name }}
            on {{ $announcement->created_at->format('F d, Y h:i A') }}
        </p>

        {{-- e() makes the text safe, nl2br() keeps the line breaks --}}
        <p>{!! nl2br(e($announcement->body)) !!}</p>

        @if ($announcement->attachment)
            @php
                $extension = strtolower(pathinfo($announcement->attachment, PATHINFO_EXTENSION));
                $isImage   = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
            @endphp

            <h2>Attachment</h2>

            @if ($isImage)
                <img src="{{ asset('storage/' . $announcement->attachment) }}" alt="Attachment of {{ $announcement->title }}"
                     style="max-width: 100%; border-radius: 8px; border: 1px solid var(--line);">
            @endif

            <p style="margin-top: 10px;">
                <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank" class="btn btn-light btn-sm">
                    Open {{ strtoupper($extension) }} file
                </a>
            </p>
        @endif
    </div>
@endsection
