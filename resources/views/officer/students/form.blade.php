@php
    // Work out which institute to preselect, from the old input or the saved program.
    $currentProgramId   = old('program_id', $student->program_id ?? '');
    $currentInstituteId = $programs->firstWhere('id', (int) $currentProgramId)?->institute_id;
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if (isset($student))
        @method('PUT')
    @endif

    <div class="field-row">
        <div>
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->user->first_name ?? '') }}" required>
        </div>
        <div>
            <label for="middle_name">Middle name (optional)</label>
            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $student->user->middle_name ?? '') }}">
        </div>
        <div>
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->user->last_name ?? '') }}" required>
        </div>
    </div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" required>

    <div class="field-row">
        <div>
            <label for="student_no">Student number</label>
            <input type="text" id="student_no" name="student_no" value="{{ old('student_no', $student->student_no ?? '') }}" required>
        </div>
        <div>
            <label for="sex">Sex</label>
            <select id="sex" name="sex" required>
                <option value="">Select</option>
                @foreach (['Male', 'Female'] as $option)
                    <option value="{{ $option }}" @selected(old('sex', $student->sex ?? '') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="year_level">Year level</label>
            <select id="year_level" name="year_level" required>
                <option value="">Select</option>
                @for ($year = 1; $year <= 6; $year++)
                    <option value="{{ $year }}" @selected((int) old('year_level', $student->year_level ?? 0) === $year)>{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- The institute dropdown has no name: it only filters the program list below --}}
    <label for="institute_select">Institute</label>
    <select id="institute_select" data-institute-select data-target="#program_id">
        <option value="">All institutes</option>
        @foreach ($institutes as $institute)
            <option value="{{ $institute->id }}" @selected((string) $currentInstituteId === (string) $institute->id)>{{ $institute->code }} - {{ $institute->name }}</option>
        @endforeach
    </select>

    <label for="program_id">Program</label>
    <select id="program_id" name="program_id" required>
        <option value="">Select a program</option>
        @foreach ($programs as $program)
            <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}"
                @selected((string) $currentProgramId === (string) $program->id)>{{ $program->code }} - {{ $program->name }}</option>
        @endforeach
    </select>

    @unless (isset($student))
        <p class="hint">The temporary password is the student number. The student can change it in My Profile.</p>
    @endunless

    <button type="submit" class="btn" style="margin-top: 20px;">{{ isset($student) ? 'Save changes' : 'Add student' }}</button>
    <a href="{{ route('officer.students.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

@include('officer.students.program-filter')
