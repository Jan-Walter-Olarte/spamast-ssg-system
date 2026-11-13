@extends('layouts.app')

@section('title', 'My Fines')

@section('content')
    <div class="page-head">
        <h1>My Fines</h1>
    </div>

    @if (! $student)
        <div class="alert alert-warning">
            Your account is not linked to a student record yet. Please ask an Officer to add you as a student.
        </div>
    @else
        <div class="grid" style="margin-bottom: 20px;">
            <div class="card"><div class="num">&#8369;{{ number_format($totalUnpaid, 2) }}</div>Still unpaid</div>
            <div class="card"><div class="num">&#8369;{{ number_format($totalAll, 2) }}</div>Total of all fines</div>
            <div class="card"><div class="num">{{ $penalties->total() }}</div>Events with a fine</div>
        </div>

        <div class="panel panel-wide">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Missed scans</th>
                            <th>Amount</th>
                            <th>Sanction</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penalties as $penalty)
                            <tr>
                                <td><a href="{{ route('events.show', $penalty->event_id) }}">{{ $penalty->event->title }}</a></td>
                                <td>{{ $penalty->event->event_date->format('M d, Y') }}</td>
                                <td>{{ $penalty->missed_scans }}</td>
                                <td>&#8369;{{ number_format((float) $penalty->amount, 2) }}</td>
                                <td>{{ blank($penalty->sanction) ? '-' : $penalty->sanction }}</td>
                                <td>
                                    @php
                                        $badge = ['unpaid' => 'badge-warning', 'paid' => 'badge-success', 'served' => 'badge-info'][$penalty->status];
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ Str::headline($penalty->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">You have no fines. Great job attending your events!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
    @endif
@endsection
