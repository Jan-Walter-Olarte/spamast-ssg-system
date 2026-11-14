@extends('layouts.app')

@section('title', 'Import Students')

@section('content')
    @php
        $fatal       = session('import_fatal');
        $errorList   = session('import_errors');
        $errorCount  = session('import_error_count');
        $totalRows   = session('import_total');
    @endphp

    <div class="page-head">
        <h1>Import Students</h1>
        <a href="{{ route('officer.students.index') }}" class="btn btn-light">Back to students</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($fatal)
        <div class="alert alert-error"><strong>The file could not be imported.</strong> {{ $fatal }}</div>
    @endif

    {{-- The row-by-row report: shown when some rows have problems --}}
    @if ($errorList)
        <div class="alert alert-error">
            <strong>Nothing was imported.</strong>
            {{ $errorCount }} of {{ $totalRows }} row(s) have problems. Fix them in your file and upload it again.
            @if ($errorCount > count($errorList))
                Showing the first {{ count($errorList) }}.
            @endif
        </div>

        <div class="panel panel-wide" style="margin-bottom: 20px;">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th>Student</th>
                            <th>What to fix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($errorList as $problem)
                            <tr>
                                <td>{{ $problem['line'] }}</td>
                                <td>{{ $problem['who'] }}</td>
                                <td>
                                    @foreach ($problem['messages'] as $message)
                                        <div>{{ $message }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="panel panel-wide">
        <h2 style="margin-top: 0;">1. Prepare your file</h2>
        <p>Use a <strong>CSV</strong> file (in Excel: <em>Save As</em>, then <em>CSV</em>). The first line must be a <strong>header row</strong>. The order of the columns does not matter.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Column</th><th>Required?</th><th>What to write</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>first_name</code></td><td>Yes</td><td>First name</td></tr>
                    <tr><td><code>middle_name</code></td><td>No</td><td>Middle name (leave empty if none)</td></tr>
                    <tr><td><code>last_name</code></td><td>Yes</td><td>Last name</td></tr>
                    <tr><td><code>email</code></td><td>Yes</td><td>Unique email address</td></tr>
                    <tr><td><code>student_no</code></td><td>Yes</td><td>Unique student number, for example 2026-1001</td></tr>
                    <tr><td><code>sex</code></td><td>Yes</td><td>Male or Female</td></tr>
                    <tr><td><code>year_level</code></td><td>Yes</td><td>1 to 6</td></tr>
                    <tr><td><code>program_code</code></td><td>Yes</td><td>The code of an existing program, for example BSIT</td></tr>
                </tbody>
            </table>
        </div>

        <p style="margin-top: 14px;">Example:</p>
<pre style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); overflow-x: auto; font-size: 0.85rem;">first_name,middle_name,last_name,email,student_no,sex,year_level,program_code
Maria,Cruz,Santos,maria.santos@example.edu.ph,2026-1001,Female,2,BSIT
Juan,,Dela Cruz,juan.delacruz@example.edu.ph,2026-1002,Male,1,BSA</pre>

        <p class="hint">
            Up to {{ $maxRows }} students per file. Every student gets an active account, and the <strong>student number is the temporary password</strong>
            (they must change it at first login). If any row has a problem, nothing is imported.
        </p>

        <h2>2. Upload it</h2>

        <form method="POST" action="{{ route('officer.students.import.store') }}" enctype="multipart/form-data">
            @csrf

            <label for="file">CSV file</label>
            <input type="file" id="file" name="file" accept=".csv,text/csv,text/plain" required>

            <button type="submit" class="btn" style="margin-top: 20px;">Import students</button>
            <a href="{{ route('officer.students.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
        </form>
    </div>
@endsection
