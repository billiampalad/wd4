@props([
    'id' => 'customAlertModal',
    'type' => 'danger', // danger, warning, info, success
    'title' => 'Nonaktifkan Akun',
    'message' => 'Apakah Anda yakin ingin menonaktifkan akun ini? Tindakan ini akan membatasi akses pengguna ke sistem.',
    'cancelText' => 'Batal',
    'confirmText' => 'Nonaktifkan',
    'confirmColor' => 'danger', // danger, primary, warning
    'formId' => null,
    'action' => null,
    'method' => 'POST',
])

<div id="{{ $id }}" class="custom-alert-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-desc">
    <div class="custom-alert-card" role="document">
        
        {{-- Icon Area --}}
        <div class="custom-alert-icon-wrapper" data-alert-type="{{ $type }}">
            <div class="custom-alert-icon-bg">
                <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Soft rounded warning triangle --}}
                    <path d="M21.1716 7.75736C22.6719 5.25686 25.3281 5.25686 26.8284 7.75736L43.4558 35.4697C44.9561 37.9702 43.628 40 40.7274 40H7.27258C4.37202 40 3.04388 37.9702 4.54416 35.4697L21.1716 7.75736Z" 
                          fill="currentColor" />
                    {{-- Exclamation mark --}}
                    <path d="M24 16V26" stroke="white" stroke-width="3.2" stroke-linecap="round" />
                    <circle cx="24" cy="32" r="1.8" fill="white" />
                </svg>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="custom-alert-content">
            <h3 id="{{ $id }}-title" class="custom-alert-title">
                {{ $title ?? $slot }}
            </h3>
            <p id="{{ $id }}-desc" class="custom-alert-message">
                {{ $message }}
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="custom-alert-actions">
            <button type="button" class="custom-alert-btn custom-alert-btn-cancel" data-alert-cancel>
                {{ $cancelText }}
            </button>
            
            @if($action)
                <form id="{{ $id }}-form" action="{{ $action }}" method="POST" class="custom-alert-form">
                    @csrf
                    @if(strtoupper($method) !== 'POST')
                        @method($method)
                    @endif
                    <button type="submit" class="custom-alert-btn custom-alert-btn-confirm custom-alert-btn-{{ $confirmColor }}" data-alert-confirm>
                        {{ $confirmText }}
                    </button>
                </form>
            @else
                <button type="button" class="custom-alert-btn custom-alert-btn-confirm custom-alert-btn-{{ $confirmColor }}" data-alert-confirm @if($formId) data-form-target="{{ $formId }}" @endif>
                    {{ $confirmText }}
                </button>
            @endif
        </div>

    </div>
</div>
