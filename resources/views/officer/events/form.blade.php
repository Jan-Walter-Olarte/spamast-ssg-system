@php
    $sessionMap = $sessionMap ?? collect();

    // The value of one time box: the old input first, otherwise the saved time (HH:MM).
    $time = fn ($period, $field) => old(
        "sessions.$period.$field",
        isset($sessionMap[$period]) ? substr((string) $sessionMap[$period]->$field, 0, 5) : ''
    );

    $sessionBlocks = [
        'morning'   => 'Morning session',
        'afternoon' => 'Afternoon session',
        'half_day'  => 'Half-day session',
    ];
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if (isset($event))
        @method('PUT')
    @endif

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title', $event->title ?? '') }}" required>

    <label for="description">Description (optional)</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $event->description ?? '') }}</textarea>

    <div class="field-row">
        <div>
            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue" value="{{ old('venue', $event->venue ?? '') }}" required>
        </div>
        <div>
            <label for="event_date">Date</label>
            <input type="date" id="event_date" name="event_date"
                   value="{{ old('event_date', isset($event) ? $event->event_date->format('Y-m-d') : '') }}" required>
        </div>
    </div>

    <div class="field-row">
        <div>
            <label for="day_type">Day type</label>
            <select id="day_type" name="day_type" required>
                <option value="">Select</option>
                <option value="whole_day" @selected(old('day_type', $event->day_type ?? '') === 'whole_day')>Whole day (morning and afternoon)</option>
                <option value="half_day" @selected(old('day_type', $event->day_type ?? '') === 'half_day')>Half day (one session)</option>
            </select>
        </div>
        <div>
            <label for="availability">Availability</label>
            <select id="availability" name="availability" required>
                <option value="scheduled" @selected(old('availability', $event->availability ?? 'scheduled') === 'scheduled')>Scheduled (by the clock)</option>
                <option value="manual" @selected(old('availability', $event->availability ?? 'scheduled') === 'manual')>Manual (an Officer opens it)</option>
            </select>
        </div>
        <div>
            <label for="fine_per_miss">Fine per missed scan</label>
            <input type="number" id="fine_per_miss" name="fine_per_miss" min="0" step="0.01"
                   value="{{ old('fine_per_miss', $event->fine_per_miss ?? $defaultFine) }}"
        </div>
    </div>

    {{-- One block per session. The script below shows only the blocks the day type needs. --}}
    @foreach ($sessionBlocks as $period => $label)
        <div id="session-{{ $period }}" style="display: none;">
            <h2>{{ $label }}</h2>
            <div class="field-row">
                <div>
                    <label for="{{ $period }}_in_start">Check-in starts</label>
                    <input type="time" id="{{ $period }}_in_start" name="sessions[{{ $period }}][check_in_start]" value="{{ $time($period, 'check_in_start') }}">
                </div>
                <div>
                    <label for="{{ $period }}_in_end">Check-in ends</label>
                    <input type="time" id="{{ $period }}_in_end" name="sessions[{{ $period }}][check_in_end]" value="{{ $time($period, 'check_in_end') }}">
                </div>
                <div>
                    <label for="{{ $period }}_out_start">Check-out starts</label>
                    <input type="time" id="{{ $period }}_out_start" name="sessions[{{ $period }}][check_out_start]" value="{{ $time($period, 'check_out_start') }}">
                </div>
                <div>
                    <label for="{{ $period }}_out_end">Check-out ends</label>
                    <input type="time" id="{{ $period }}_out_end" name="sessions[{{ $period }}][check_out_end]" value="{{ $time($period, 'check_out_end') }}">
                </div>
            </div>
        </div>
    @endforeach
    <p class="hint">Times are required for scheduled events and optional for manual events.</p>

    <button type="submit" class="btn" style="margin-top: 20px;">{{ isset($event) ? 'Save changes' : 'Create event' }}</button>
    <a href="{{ route('events.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

<script>
    (function () {
        var dayType = document.getElementById('day_type');
        var availability = document.getElementById('availability');

        // Show or hide one session block. Hidden inputs are disabled, so they are not submitted.
        function toggle(period, show) {
            var box = document.getElementById('session-' + period);
            box.style.display = show ? '' : 'none';
            box.querySelectorAll('input').forEach(function (input) {
                input.disabled = !show;
                input.required = show && availability.value === 'scheduled';
            });
        }

        function sync() {
            toggle('morning', dayType.value === 'whole_day');
            toggle('afternoon', dayType.value === 'whole_day');
            toggle('half_day', dayType.value === 'half_day');
        }

        dayType.addEventListener('change', sync);
        availability.addEventListener('change', sync);
        sync();
    })();
</script>
