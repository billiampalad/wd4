@props([
    'variant' => 'modal', // 'modal' | 'toast' | 'banner'
    'id' => null,
    'type' => 'danger', // danger, warning, info, success, primary, logout
    'title' => null,
    'message' => null,
    'cancelText' => 'Batal',
    'confirmText' => null,
    'confirmColor' => null,
    'formId' => null,
    'action' => null,
    'method' => 'POST',
    'dismissible' => true,
    'showCancel' => true,
])

@php
    $id = $id ?? ('customAlert_' . uniqid());

    // Default values based on type
    if ($type === 'success') {
        $title = $title ?? 'Berhasil!';
        $message = $message ?? 'Tindakan Anda telah berhasil diproses.';
        $confirmText = $confirmText ?? 'OK';
        $confirmColor = $confirmColor ?? 'success';
    } elseif ($type === 'info' || $type === 'information') {
        $title = $title ?? 'Informasi';
        $message = $message ?? 'Informasi sistem terbaru.';
        $confirmText = $confirmText ?? 'Mengerti';
        $confirmColor = $confirmColor ?? 'info';
    } elseif ($type === 'warning') {
        $title = $title ?? 'Peringatan';
        $message = $message ?? 'Harap perhatikan peringatan ini sebelum melanjutkan.';
        $confirmText = $confirmText ?? 'Lanjutkan';
        $confirmColor = $confirmColor ?? 'warning';
    } elseif ($type === 'logout') {
        $title = $title ?? 'Keluar dari Sistem';
        $message = $message ?? 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?';
        $confirmText = $confirmText ?? 'Keluar';
        $confirmColor = $confirmColor ?? 'danger';
    } else {
        $title = $title ?? 'Nonaktifkan Akun';
        $message = $message ?? 'Apakah Anda yakin ingin menonaktifkan akun ini? Tindakan ini akan membatasi akses pengguna ke sistem.';
        $confirmText = $confirmText ?? 'Nonaktifkan';
        $confirmColor = $confirmColor ?? ($type === 'danger' ? 'danger' : $type);
    }
@endphp

@if($variant === 'toast' || $variant === 'banner')
    {{-- ── Toast / Banner Notification Layout (Sesuai Referensi Gambar) ── --}}
    <div id="{{ $id }}" class="custom-toast-card custom-toast-{{ $type }}" role="alert" aria-live="polite">
        <div class="custom-toast-icon-box">
            @if($type === 'success')
                <svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9.5" stroke="#10b981" stroke-width="2" fill="none" />
                    <path d="M8 12.2L10.8 15L16 9.5" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            @elseif($type === 'info' || $type === 'information' || $type === 'primary')
                <svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9.5" stroke="#0ea5e9" stroke-width="2" fill="none" />
                    <path d="M12 11V16" stroke="#0ea5e9" stroke-width="2.2" stroke-linecap="round" />
                    <circle cx="12" cy="8" r="1.2" fill="#0ea5e9" />
                </svg>
            @elseif($type === 'warning')
                <svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.8 4.2C11.3 3.3 12.7 3.3 13.2 4.2L20.8 17.5C21.3 18.4 20.6 19.5 19.6 19.5H4.4C3.4 19.5 2.7 18.4 3.2 17.5L10.8 4.2Z" stroke="#f59e0b" stroke-width="2" fill="none" />
                    <path d="M12 9.5V13.5" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" />
                    <circle cx="12" cy="16.5" r="1.2" fill="#f59e0b" />
                </svg>
            @else
                {{-- Error / Danger --}}
                <svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9.5" stroke="#ef4444" stroke-width="2" fill="none" />
                    <path d="M12 8V12.5" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" />
                    <circle cx="12" cy="15.5" r="1.2" fill="#ef4444" />
                </svg>
            @endif
        </div>

        <div class="custom-toast-body">
            <h4 class="custom-toast-title">{{ $title }}</h4>
            <p class="custom-toast-desc">{!! $message !!}</p>
        </div>

        @if($dismissible)
            <button type="button" class="custom-toast-close" data-toast-close onclick="this.closest('.custom-toast-card').remove()" aria-label="Tutup notifikasi">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        @endif
    </div>

@else
    {{-- ── Centered Modal Confirmation Dialog ── --}}
    <div id="{{ $id }}" class="custom-alert-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-desc">
        <div class="custom-alert-card" role="document">
            
            {{-- Icon Area --}}
            <div class="custom-alert-icon-wrapper" data-alert-type="{{ $type }}">
                <div class="custom-alert-icon-bg">
                    @if($type === 'success')
                        <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="19" fill="currentColor" />
                            <path d="M15 24.5L21 30.5L33 17.5" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @elseif($type === 'logout')
                        <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="19" fill="currentColor" />
                            <path d="M22 15H16C14.8954 15 14 15.8954 14 17V31C14 32.1046 14.8954 33 16 33H22" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M28 18L34 24L28 30" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 24H33" stroke="white" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    @elseif($type === 'primary' || $type === 'info')
                        <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="19" fill="currentColor" />
                            <path d="M24 21V33" stroke="white" stroke-width="3.5" stroke-linecap="round" />
                            <circle cx="24" cy="15" r="2" fill="white" />
                        </svg>
                    @else
                        {{-- Danger / Warning Triangle --}}
                        <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.1716 7.75736C22.6719 5.25686 25.3281 5.25686 26.8284 7.75736L43.4558 35.4697C44.9561 37.9702 43.628 40 40.7274 40H7.27258C4.37202 40 3.04388 37.9702 4.54416 35.4697L21.1716 7.75736Z" 
                                  fill="currentColor" />
                            <path d="M24 16V26" stroke="white" stroke-width="3.2" stroke-linecap="round" />
                            <circle cx="24" cy="32" r="1.8" fill="white" />
                        </svg>
                    @endif
                </div>
            </div>

            {{-- Content Area --}}
            <div class="custom-alert-content">
                <h3 id="{{ $id }}-title" class="custom-alert-title">
                    {{ $title ?? $slot }}
                </h3>
                <p id="{{ $id }}-desc" class="custom-alert-message">
                    {!! $message !!}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="custom-alert-actions">
                @if($showCancel)
                    <button type="button" class="custom-alert-btn custom-alert-btn-cancel" data-alert-cancel>
                        {{ $cancelText }}
                    </button>
                @endif
                
                @if($action)
                    <form id="{{ $id }}-form" action="{{ $action }}" method="POST" class="custom-alert-form" @if($type === 'logout') data-turbo="false" @endif>
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
@endif

