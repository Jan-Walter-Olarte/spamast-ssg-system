@extends('layouts.app')

@section('title', 'Event Report')

@section('content')
    <div class="page-head no-print">
        <h1>Event Report</h1>
        <div class="actions">
            <a href="{{ route('reports.index') }}" class="btn btn-light">Back to reports</a>
            <button type="button" class="btn" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="panel panel-wide">

        {{-- Heading of the report (this part also prints) --}}
        <div class="report-block">
            <h1>{{ $schoolName }}</h1>
            <h2 style="margin-top: 4px;">Event Attendance Report</h2>
            <p>
                <strong>{{ $event->title }}</strong><br>
                {{ $event->event_date->format('F d, Y') }} &middot; {{ $event->venue }}<br>
                {{ Str::headline($event->day_type) }} &middot;
                {{ $event->sessions_count * 2 }} scans required per student &middot;
                &#8369;{{ number_format((float) $event->fine_per_miss, 2) }} per missed scan
            </p>
        </div>

        {{-- Headcount by institute and program --}}
        <div class="report-block">
            <h2>Students who attended (at least one check-in)</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Institute</th>
                            <th>Program</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($institutes as $instituteCode => $institute)
                            @foreach ($institute['programs'] as $row)
                                <tr>
                                    <td>{{ $row->institute_code }}</td>
                                    <td>{{ $row->program_code }}</td>
                                    <td>{{ $row->male }}</td>
                                    <td>{{ $row->female }}</td>
                                    <td>{{ $row->total }}</td>
                                </tr>
                            @endforeach

                            {{-- Subtotal of this institute --}}
                            <tr class="total-row">
                                <td colspan="2">Total {{ $instituteCode }}</td>
                                <td>{{ $institute['male'] }}</td>
                                <td>{{ $institute['female'] }}</td>
                                <td>{{ $institute['total'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No student has checked in to this event yet.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($institutes->isNotEmpty())
                        <tfoot>
                            <tr class="grand-row">
                                <th colspan="2">Grand total</th>
                                <th>{{ $grand['male'] }}</th>
                                <th>{{ $grand['female'] }}</th>
                                <th>{{ $grand['total'] }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

                {{-- Fine summary by program --}}
        <div class="report-block">
            <h2>Fine summary by program</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Students with fines</th>
                            <th>Total fines</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fineSummary as $row)
                            <tr>
                                <td>{{ $row['program'] }}</td>
                                <td>{{ $row['students'] }}</td>
                                <td>&#8369;{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No penalties yet. An Officer can generate them from the Fines page after the event.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($fineSummary->isNotEmpty())
                        <tfoot>
                            <tr class="grand-row">
                                <th>Grand total</th>
                                <th>{{ $fineSummary->sum('students') }}</th>
                                <th>&#8369;{{ number_format($grandTotal, 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Per-student fines --}}
        <div class="report-block">
            <h2>Missed scans, fines and sanctions</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student no.</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Missed scans</th>
                            <th>Fine</th>
                            <th>Sanction</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penalties as $penalty)
                            <tr>
                                <td>{{ $penalty->student->student_no }}</td>
                                <td>{{ $penalty->student->user->last_name }}, {{ $penalty->student->user->first_name }}</td>
                                <td>{{ $penalty->student->program->code }}</td>
                                <td>{{ $penalty->missed_scans }}</td>
                                <td>&#8369;{{ number_format((float) $penalty->amount, 2) }}</td>
                                <td>{{ $penalty->sanction ?: '-' }}</td>
                                <td>{{ Str::headline($penalty->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No penalties yet. An Officer can generate them from the Fines page after the event.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($penalties->isNotEmpty())
                        <tfoot>
                            <tr class="grand-row">
                                <th colspan="4">Total fines ({{ $penalties->count() }} students)</th>
                                <th>&#8369;{{ number_format((float) $fineTotal, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <p style="color: var(--muted); font-size: 0.85rem;">Printed {{ now()->format('F d, Y h:i A') }}</p>
    </div>
@endsection
