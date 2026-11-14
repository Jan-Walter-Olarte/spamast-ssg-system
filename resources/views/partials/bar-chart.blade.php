@php
    // The longest bar is 100% wide; the others are shown relative to it.
    $max = max(1, collect($items)->max('value'));
@endphp

<h2>{{ $title }}</h2>

<div class="panel panel-wide">
    @forelse ($items as $item)
        @php
            $percent = $item['value'] > 0 ? max(3, round(($item['value'] / $max) * 100)) : 0;
        @endphp

        <div class="bar-row">
            <div class="bar-label">
                {{ $item['label'] }}
                @if ($item['sub'])
                    <small>{{ $item['sub'] }}</small>
                @endif
            </div>
            <div class="bar-track"><div class="bar-fill" style="width: {{ $percent }}%;"></div></div>
            <div class="bar-value">{{ $money ? '₱' . number_format($item['value'], 2) : $item['value'] }}</div>
        </div>
    @empty
        <p style="margin: 0; color: var(--muted);">{{ $empty }}</p>
    @endforelse
</div>
