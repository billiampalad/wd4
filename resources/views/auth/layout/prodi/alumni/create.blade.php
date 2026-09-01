@extends('auth.prodi')

@section('content')
    <main id="mainContent" class="dk-page">
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('prodi.alumni.index') }}">Tracking Lulusan</a>
                    <span>/</span>
                    <span>Tambah Data Alumni</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-user-plus"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Tambah Data Alumni Bekerja di Mitra</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Catat data alumni program studi yang bekerja atau terserap di perusahaan/instansi mitra.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        @if(session('error'))
            <div class="dk-alert dk-alert-error" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="dk-alert dk-alert-error" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Terdapat kesalahan input:</strong>
                    <ul style="margin: 4px 0 0 16px; padding: 0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card um-card dk-card" style="width: 100%; max-width: 100%; box-sizing: border-box; border-radius: 16px; overflow: visible;">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-id-card"></i></span>
                    <span>
                        <strong>Formulir Data Alumni &amp; Penyerapan</strong>
                        <small>Lengkapi data identitas alumni, tahun kelulusan, serta data penempatan di mitra industri.</small>
                    </span>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0; width: 100%; box-sizing: border-box; overflow: visible;">
                <form action="{{ route('prodi.alumni.store') }}" method="POST" style="width: 100%; box-sizing: border-box;">
                    @csrf

                    {{-- ═══ TWO-COLUMN TOP LAYOUT (Width Constrained & Non-Overflowing) ═══ --}}
                    <div style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">

                        {{-- ══ LEFT COLUMN: Periode & Kelulusan (Sticky) ══ --}}
                        <div style="position: sticky; top: 24px; align-self: start; min-width: 0; max-width: 100%; box-sizing: border-box;">
                            <div
                                style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible; width: 100%; box-sizing: border-box;">
                                <div x-data="{ showPeriode: true }">
                                    {{-- Card Header --}}
                                    <div @click="showPeriode = !showPeriode"
                                        style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                        <div
                                            style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(5,150,105,0.25);">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <h4
                                                style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
                                                Periode &amp; Kelulusan
                                            </h4>
                                        </div>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 10px; color: var(--text-sub); transition: transform 0.3s ease; flex-shrink: 0;"
                                            :style="showPeriode ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>

                                    {{-- Card Body --}}
                                    <div x-show="showPeriode" x-collapse.duration.300ms style="padding: 18px; width: 100%; box-sizing: border-box;">

                                        {{-- ── Tahun Lulus ── --}}
                                        <div style="margin-bottom: 16px;">
                                            <div class="mc-group">
                                                <label class="mc-label">Tahun Lulus <span class="mc-req">*</span></label>
                                                <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                    <i class="fas fa-calendar-check mc-icon-left"
                                                        style="color: #059669;"></i>
                                                    <input type="number" name="tahun_lulus"
                                                        value="{{ old('tahun_lulus', date('Y')) }}" required min="2000" max="2099"
                                                        class="mc-input" style="width: 100%; box-sizing: border-box;" placeholder="Contoh: 2024" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── Tahun Mulai Bekerja ── --}}
                                        <div style="margin-bottom: 16px;">
                                            <div class="mc-group">
                                                <label class="mc-label">Tahun Mulai Bekerja <span class="mc-req">*</span></label>
                                                <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                    <i class="fas fa-briefcase mc-icon-left"
                                                        style="color: #4f46e5;"></i>
                                                    <input type="number" name="tahun_mulai"
                                                        value="{{ old('tahun_mulai', date('Y')) }}" required min="2000" max="2099"
                                                        class="mc-input" style="width: 100%; box-sizing: border-box;" placeholder="Contoh: 2024" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Divider --}}
                                        <div
                                            style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 18px 0;">
                                        </div>

                                        {{-- Panduan Singkat --}}
                                        <div
                                            style="background: rgba(79,70,229,0.04); border: 1px dashed rgba(79,70,229,0.25); border-radius: 12px; padding: 14px; width: 100%; box-sizing: border-box;">
                                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                                <i class="fas fa-circle-info"
                                                    style="color: #4f46e5; margin-top: 2px; font-size: 14px; flex-shrink: 0;"></i>
                                                <div style="font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                                    Data alumni digunakan untuk indikator kinerja utama (IKU) keterikatan dan penyerapan lulusan di dunia usaha dan industri (DUDIKA).
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ RIGHT COLUMN: Form Utama ══ --}}
                        <div style="min-width: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
                            <div class="mc-body" style="padding: 0; width: 100%; max-width: 100%; box-sizing: border-box;">

                                {{-- ── SEKSI 1: DATA IDENTITAS ALUMNI ── --}}
                                <div style="margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                        <div
                                            style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #4f46e5, #818cf8); flex-shrink: 0;">
                                        </div>
                                        <span
                                            style="font-weight: 700; font-size: 14px; color: var(--text); letter-spacing: 0.01em;">
                                            Data Identitas Alumni
                                        </span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; width: 100%; box-sizing: border-box;">
                                        {{-- NIM Alumni --}}
                                        <div class="mc-group" style="min-width: 0; width: 100%; box-sizing: border-box;">
                                            <label class="mc-label">NIM Alumni <span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                <i class="fas fa-id-card mc-icon-left" style="color: #4f46e5;"></i>
                                                <input type="text" name="nim" value="{{ old('nim') }}" required
                                                    placeholder="Contoh: 21021001" class="mc-input" style="width: 100%; box-sizing: border-box;">
                                            </div>
                                        </div>

                                        {{-- Nama Lengkap Alumni --}}
                                        <div class="mc-group" style="min-width: 0; width: 100%; box-sizing: border-box;">
                                            <label class="mc-label">Nama Lengkap Alumni <span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                <i class="fas fa-user mc-icon-left" style="color: #059669;"></i>
                                                <input type="text" name="nama" value="{{ old('nama') }}" required
                                                    placeholder="Nama Lengkap Alumni" class="mc-input" style="width: 100%; box-sizing: border-box;">
                                            </div>
                                        </div>

                                        {{-- Nomor Telepon / WhatsApp --}}
                                        <div class="mc-group" style="min-width: 0; width: 100%; box-sizing: border-box;">
                                            <label class="mc-label">Nomor Telepon / WhatsApp</label>
                                            <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                <i class="fab fa-whatsapp mc-icon-left" style="color: #10b981;"></i>
                                                <input type="text" name="telepon" value="{{ old('telepon') }}"
                                                    placeholder="08xxxxxxxxxx" class="mc-input" style="width: 100%; box-sizing: border-box;">
                                            </div>
                                        </div>

                                        {{-- Email Alumni --}}
                                        <div class="mc-group" style="min-width: 0; width: 100%; box-sizing: border-box;">
                                            <label class="mc-label">Email Alumni</label>
                                            <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                <i class="fas fa-envelope mc-icon-left" style="color: #6366f1;"></i>
                                                <input type="email" name="email" value="{{ old('email') }}"
                                                    placeholder="alumni@email.com" class="mc-input" style="width: 100%; box-sizing: border-box;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── SEKSI 2: INFORMASI PENYERAPAN DI INSTANSI MITRA ── --}}
                                <div
                                    style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 22px; margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
                                        <div
                                            style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text);">
                                                Informasi Penyerapan di Instansi Mitra
                                            </h4>
                                            <small style="color: var(--text-sub); font-size: 12px;">Pilih perusahaan atau instansi mitra tempat alumni bekerja dan posisinya.</small>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; width: 100%; box-sizing: border-box;">
                                        {{-- Mitra Industri (Searchable Alpine Dropdown) --}}
                                        <div class="mc-group" style="grid-column: 1 / -1; min-width: 0; width: 100%; box-sizing: border-box;" x-data="{
                                            open: false,
                                            search: '',
                                            selectedId: '{{ old('mitra_id') }}',
                                            items: [
                                                @foreach($mitras as $mitra)
                                                    { id: '{{ $mitra->id }}', name: '{{ addslashes($mitra->nama_mitra) }}' },
                                                @endforeach
                                            ],
                                            get filteredItems() {
                                                if (!this.search) return this.items;
                                                const q = this.search.toLowerCase();
                                                return this.items.filter(i => i.name.toLowerCase().includes(q));
                                            },
                                            get selectedItem() {
                                                return this.items.find(i => String(i.id) === String(this.selectedId));
                                            }
                                        }">
                                            <label class="mc-label">Perusahaan / Instansi Mitra <span class="mc-req">*</span></label>
                                            <input type="hidden" name="mitra_id" :value="selectedId" required>

                                            <div class="alpine-dropdown" @click.outside="open = false"
                                                style="position: relative; width: 100%; box-sizing: border-box;">
                                                <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                                    style="min-height: 44px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; transition: all 0.2s; width: 100%; box-sizing: border-box;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; overflow: hidden;">
                                                        <div
                                                            style="width: 28px; height: 28px; border-radius: 6px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                        <span x-show="selectedItem"
                                                            x-text="selectedItem ? selectedItem.name : ''"
                                                            style="font-weight: 700; font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                                                        <span x-show="!selectedItem"
                                                            style="color: #9ca3af; font-size: 13px;">— Pilih Mitra Tempat Bekerja —</span>
                                                    </div>
                                                    <i class="fas fa-chevron-down"
                                                        style="font-size: 10px; color: #9ca3af; transition: 0.3s; flex-shrink: 0;"
                                                        :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                                </div>

                                                <div class="ad-menu" x-show="open" x-transition
                                                    style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 120; max-height: 250px; overflow-y: auto; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); width: 100%; box-sizing: border-box;">
                                                    <div
                                                        style="padding: 8px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                        <input type="text" x-model="search"
                                                            placeholder="Cari nama mitra industri..."
                                                            style="width: 100%; padding: 8px 12px; font-size: 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface2); color: var(--text); box-sizing: border-box;"
                                                            @click.stop>
                                                    </div>
                                                    <template x-for="item in filteredItems" :key="item.id">
                                                        <div class="ad-item"
                                                            :class="{'selected': String(selectedId) === String(item.id)}"
                                                            @click="selectedId = item.id; open = false;"
                                                            style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer;">
                                                            <strong x-text="item.name"
                                                                style="font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 8px;"></strong>
                                                            <i class="fas fa-check" style="color: #4f46e5; font-size: 11px; flex-shrink: 0;"
                                                                x-show="String(selectedId) === String(item.id)"></i>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItems.length === 0"
                                                        style="padding: 12px; text-align: center; color: var(--text-sub); font-size: 12px;">
                                                        Mitra industri tidak ditemukan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Posisi / Jabatan Pekerjaan (Full Width) --}}
                                        <div class="mc-group" style="grid-column: 1 / -1; min-width: 0; width: 100%; box-sizing: border-box;">
                                            <label class="mc-label">Posisi / Jabatan Pekerjaan <span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap" style="width: 100%; box-sizing: border-box;">
                                                <i class="fas fa-user-tie mc-icon-left" style="color: #4f46e5;"></i>
                                                <input type="text" name="posisi" value="{{ old('posisi') }}" required
                                                    placeholder="Contoh: Software Engineer, Staff IT, Network Administrator"
                                                    class="mc-input" style="width: 100%; box-sizing: border-box;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── FOOTER ACTIONS ── --}}
                                <div
                                    style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); width: 100%; box-sizing: border-box;">
                                    <a href="{{ route('prodi.alumni.index') }}" class="rfc-btn"
                                        style="background: var(--surface2); color: var(--text); border: 1.5px solid var(--border); padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-arrow-left"></i> Batal
                                    </a>
                                    <button type="submit" class="rfc-btn rfc-btn-primary"
                                        style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-save"></i> Simpan Data Alumni
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
