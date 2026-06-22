@props([
    'text' => 'Memuat...',
    'size' => 50,
])

@once
    <link rel="stylesheet" href="{{ asset('css/loading.css') }}">
@endonce

<div
    {{ $attributes }}
    role="status"
    aria-live="polite"
    style="display: inline-flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; color: #64748b; text-align: center;"
>
    <svg
        class="container"
        style="--uib-size: {{ (int) $size }}px;"
        viewBox="0 0 50 50"
        aria-hidden="true"
    >
        <circle class="track" cx="25" cy="25" r="20"></circle>
        <circle class="car" cx="25" cy="25" r="20"></circle>
    </svg>

    @if ($text)
        <span style="font-size: 13px; line-height: 1.4;">{{ $text }}</span>
    @endif
</div>
