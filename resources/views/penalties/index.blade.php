@extends('layouts.app')

@section('title', 'Fines and Sanctions')

@section('content')
    <div class="page-head">
        <div>
            <h1>Fines and Sanctions</h1>
            <p style="margin: 0; color: var(--muted);">
                {{ $event->title }} &middot; {{ $event->event_date->format('M d, Y') }} &middot;
                {{ $event->sessions_count * 2 }} scans required per student &middot;
                &#8369;{{ number_format((float) $event->fine_per_miss, 2) }} per missed scan
            </p>
        </div>
        <div class="actions">
            <a href="{{ route('events.show', $event) }}" class="btn btn-light">Back to event</a>

            {{-- Officers only: calculate the fines --}}
            @can('manage-ssg')
                <form method="POST" action="{{ route('officer.penalties.generate', $event) }}" class="inline-form"
                      onsubmit="return confirm('Calculate fines for every student? Existing amounts will be recalculated, but sanctions and statuses are kept.');">
                    @csrf
                    <button type="submit" class="btn">Generate fines</button>
                </form>
            @endcan
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Filters (a GET form, so the address keeps them) --}}
    <form method="GET" action="{{ route('penalties.index', $event) }}" class="toolbar">
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

        <select name="status">
            <option value="">All statuses</option>
            @foreach (['unpaid' => 'Unpaid', 'paid' => 'Paid', 'served' => 'Served'] as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('penalties.index', $event) }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student no.</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Institute</th>
                        <th>Missed scans</th>
                        <th>Amount</th>
                        <th>Sanction</th>
                        <th>Status</th>
                        @can('manage-ssg')
                            <th>Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penalties as $penalty)
                        <tr>
                            <td>{{ $penalty->student->student_no }}</td>
                            <td>{{ $penalty->student->user->last_name }}, {{ $penalty->student->user->first_name }}</td>
                            <td>{{ $penalty->student->program->code }}</td>
                            <td><span class="badge badge-info">{{ $penalty->student->program->institute->code }}</span></td>
                            <td>{{ $penalty->missed_scans }}</td>
                            <td>&#8369;{{ number_format((float) $penalty->amount, 2) }}</td>
                            <td>{{ $penalty->sanction ?: '-' }}</td>
                            <td>
                                @php
                                    $badge = ['unpaid' => 'badge-warning', 'paid' => 'badge-success', 'served' => 'badge-info'][$penalty->status];
                                @endphp
                                <span class="badge {{ $badge }}">{{ Str::headline($penalty->status) }}</span>
                            </td>

                            {{-- Officer controls: Advisers never see these --}}
                            @can('manage-ssg')
                                <td>
                                    <details>
                                        <summary>Edit</summary>
                                        <form method="POST" action="{{ route('officer.penalties.update', $penalty) }}" class="inline-edit">
                                            @csrf
                                            @method('PUT')

                                            <label>Amount</label>
                                            <input type="number" name="amount" min="0" step="0.01" value="{{ $penalty->amount }}" required>

                                            <label>Sanction (optional)</label>
                                            <input type="text" name="sanction" maxlength="255" value="{{ $penalty->sanction }}" placeholder="For example, community service">

                                            <label>Status</label>
                                            <select name="status" required>
                                                @foreach (['unpaid' => 'Unpaid', 'paid' => 'Paid', 'served' => 'Served'] as $value => $label)
                                                    <option value="{{ $value }}" @selected($penalty->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>

                                            <button type="submit" class="btn btn-sm">Save</button>
                                        </form>
                                    </details>

                                    <form method="POST" action="{{ route('officer.penalties.destroy', $penalty) }}" class="inline-form"
                                          onsubmit="return confirm('Delete this penalty? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="margin-top: 6px;">Delete</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="9">No penalties found. An Officer can click "Generate fines" after the event.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Total ({{ $penalties->total() }} students)</th>
                        <th>&#8369;{{ number_format((float) $total, 2) }}</th>
                        <th colspan="{{ auth()->user()->can('manage-ssg') ? 3 : 2 }}"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($penalties->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $penalties->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $penalties->currentPage() }} of {{ $penalties->lastPage() }}</span>

            @if ($penalties->hasMorePages())
                <a href="{{ $penalties->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>

    {{-- Reuses the institute-to-program filter script from the Students page --}}
    @include('officer.students.program-filter')
@endsection
