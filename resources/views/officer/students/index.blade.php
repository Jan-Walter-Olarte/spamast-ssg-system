@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="page-head">
        <h1>Students</h1>
        <div class="actions">
            <a href="{{ route('officer.students.import') }}" class="btn btn-light">Import CSV</a>
            <a href="{{ route('officer.students.create') }}" class="btn">Add Student</a>
        </div>
    </div>

    {{-- Search and filters (a GET form, so the address keeps them) --}}
    <form method="GET" action="{{ route('officer.students.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name or student no.">

        <select name="institute" id="filter_institute" data-institute-select data-target="#filter_program">
            <option value="">All institutes</option>
            @foreach ($institutes as $institute)
                <option value="{{ $institute->id }}" @selected((string) $instituteId === (string) $institute->id)>{{ $institute->code }}</option>
            @endforeach
        </select>

        <select name="program" id="filter_program">
            <option value="">All programs</option>
            @foreach ($programs as $program)
                <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}"
                    @selected((string) $programId === (string) $program->id)>{{ $program->code }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('officer.students.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student no.</th>
                        <th>Name</th>
                        <th>Institute</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th>Sex</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->student_no }}</td>
                            <td>{{ $student->user->last_name }}, {{ $student->user->first_name }} {{ $student->user->middle_name }}</td>
                            <td><span class="badge badge-info">{{ $student->program->institute->code }}</span></td>
                            <td>{{ $student->program->code }}</td>
                            <td>{{ $student->year_level }}</td>
                            <td>{{ $student->sex }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('officer.students.edit', $student) }}" class="btn btn-light btn-sm">Edit</a>

                                    <form method="POST" action="{{ route('officer.students.destroy', $student) }}" class="inline-form"
                                          onsubmit="return confirm('Delete this student and their login account? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($students->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $students->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $students->currentPage() }} of {{ $students->lastPage() }} ({{ $students->total() }} students)</span>

            @if ($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>

    @include('officer.students.program-filter')
@endsection
