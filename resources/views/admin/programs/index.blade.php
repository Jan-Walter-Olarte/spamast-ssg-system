@extends('layouts.app')

@section('title', 'Programs')

@section('content')
    <div class="page-head">
        <h1>Programs</h1>
        <a href="{{ route('admin.programs.create') }}" class="btn">Add Program</a>
    </div>

    {{-- Search and institute filter (a GET form, so the address keeps them) --}}
    <form method="GET" action="{{ route('admin.programs.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search code, name or institute">
        <select name="institute">
            <option value="">All institutes</option>
            @foreach ($institutes as $institute)
                <option value="{{ $institute->id }}" @selected((string) $instituteId === (string) $institute->id)>{{ $institute->code }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('admin.programs.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Institute</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                        <tr>
                            <td><span class="badge badge-info">{{ $program->institute_code }}</span></td>
                            <td>{{ $program->code }}</td>
                            <td>{{ $program->name }}</td>
                            <td>{{ $program->students_count }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.programs.edit', $program->id) }}" class="btn btn-light btn-sm">Edit</a>

                                    <form method="POST" action="{{ route('admin.programs.destroy', $program->id) }}" class="inline-form"
                                          onsubmit="return confirm('Delete this program? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No programs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($programs->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $programs->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $programs->currentPage() }} of {{ $programs->lastPage() }} ({{ $programs->total() }} programs)</span>

            @if ($programs->hasMorePages())
                <a href="{{ $programs->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection
