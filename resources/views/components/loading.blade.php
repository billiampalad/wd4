@props([
    'text' => 'Memuat...',
    'size' => 50,
])


<div {{ $attributes->class(['app-loading-indicator']) }} role="status" aria-live="polite">
    <svg
        class="app-loading-spinner"
        style="--uib-size: {{ (int) $size }}px;"
        viewBox="0 0 50 50"
        aria-hidden="true"
    >
        <circle class="track" cx="25" cy="25" r="20"></circle>
        <circle class="car" cx="25" cy="25" r="20"></circle>
    </svg>

    @if ($text)
        <span class="app-loading-text">{{ $text }}</span>
    @endif
</div>
