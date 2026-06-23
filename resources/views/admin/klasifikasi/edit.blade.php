@extends('admin.dashboard')

@section('styles')
    <style>
        /* ─── MODERN FORM & DETAIL (Extracted from user.css) ─── */
        .modern-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.08);
        }

        .mc-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .mc-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .mc-title h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 2px 0;
        }

        .mc-title p {
            margin: 0;
            font-size: 12px;
            color: var(--text-sub);
        }

        .mc-body {
            padding: 24px;
        }

        .mc-grid-1 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .mc-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        .mc-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
        }

        .mc-req {
            color: var(--danger);
            font-weight: 800;
        }

        .mc-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .mc-icon-left {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 13px;
            z-index: 2;
            pointer-events: none;
        }

        .mc-input {
            width: 100%;
            min-height: 42px;
            padding: 8px 14px 8px 38px;
            font-size: 13px;
            color: var(--text);
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            transition: all 0.2s;
            font-family: inherit;
            box-sizing: border-box;
        }

        .mc-input:focus {
            outline: none;
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .mc-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            background: var(--surface2);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .rfc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 40px;
            padding: 0 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .rfc-btn-primary {
            background: var(--accent);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .rfc-btn-primary:hover {
            background: var(--accent2);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
            color: #ffffff;
        }

        .rfc-btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .rfc-btn-danger:hover {
            background: var(--danger);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
    </style>
@endsection

@section('content')
    <main id="mainContent" class="admin-dashboard">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('klasifikasi.index') }}" class="ud-breadcrumb-link">Klasifikasi Mitra</a>
                <span>/</span>
                <span>Edit</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Klasifikasi Mitra</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Perbarui data klasifikasi mitra di bawah ini.
                    </p>
                </div>
            </div>
        </div>
    </section>

        <div style="width: 100%; max-width: 1200px; margin: 0 auto;">
            <div class="modern-card">
                <div class="mc-header">
                    <div class="mc-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="mc-title">
                        <h3>Formulir Edit Klasifikasi</h3>
                        <p>Silakan perbarui data klasifikasi mitra di bawah ini.</p>
                    </div>
                </div>

                <form action="{{ route('klasifikasi.update', $klasifikasi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mc-body">
                        <div class="mc-grid-1">
                            <div class="mc-group">
                                <label class="mc-label">Nama Klasifikasi <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-tag mc-icon-left"></i>
                                    <input type="text" name="nama" value="{{ old('nama', $klasifikasi->nama) }}" required
                                        placeholder="Masukkan nama klasifikasi"
                                        class="mc-input @error('nama') border-danger @enderror" />
                                </div>
                                @error('nama')
                                    <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i
                                            class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mc-footer"
                        style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <a href="{{ route('klasifikasi.index') }}" class="rfc-btn rfc-btn-danger">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="rfc-btn rfc-btn-primary">
                            <i class="fas fa-save"></i> Perbarui Klasifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
