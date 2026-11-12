@extends('layouts.app')

@section('title', 'Institutes')

@section('content')
    <div class="page-head">
        <h1>Institutes</h1>
        <a href="{{ route('admin.institutes.create') }}" class="btn">Add Institute</a>
    </div>

    {{-- Search (a GET form, so the address keeps the search text) --}}
    <form method="GET" action="{{ route('admin.institutes.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search code or name">
        <button type="submit" class="btn">Search</button>
        <a href="{{ route('admin.institutes.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Programs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($institutes as $institute)
                        <tr>
                            <td>{{ $institute->code }}</td>
                            <td>{{ $institute->name }}</td>
                            <td><span class="badge badge-info">{{ $institute->programs_count }}</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.institutes.edit', $institute->id) }}" class="btn btn-light btn-sm">Edit</a>

                                    <form method="POST" action="{{ route('admin.institutes.destroy', $institute->id) }}" class="inline-form"
                                          onsubmit="return confirm('Delete this institute? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No institutes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($institutes->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $institutes->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $institutes->currentPage() }} of {{ $institutes->lastPage() }} ({{ $institutes->total() }} institutes)</span>

            @if ($institutes->hasMorePages())
                <a href="{{ $institutes->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection
