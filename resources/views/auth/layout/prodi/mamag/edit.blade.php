@extends('auth.prodi')

@section('content')
    @php
        $pembimbingInternal = $penempatan->pembimbings?->where('tipe', 'Internal')->first();
        $pembimbingEksternal = $penempatan->pembimbings?->where('tipe', 'Eksternal')->first();

        $valMulai = old('periode_mulai', $penempatan->periode_mulai ? \Carbon\Carbon::parse($penempatan->periode_mulai)->format('Y-m-d') : '');
        $valSelesai = old('periode_selesai', $penempatan->periode_selesai ? \Carbon\Carbon::parse($penempatan->periode_selesai)->format('Y-m-d') : '');
        $valMhsId = old('mahasiswa_id', $penempatan->mahasiswa_id);
        $valKegiatanId = old('kegiatan_id', $penempatan->kegiatan_id);
        $valMitraId = old('mitra_id', $penempatan->mitra_id);

        $valNamaInternal = old('nama_pembimbing_internal', $pembimbingInternal->nama_pembimbing ?? '');
        $valKontakInternal = old('kontak_pembimbing_internal', $pembimbingInternal->kontak ?? '');
        $valNamaEksternal = old('nama_pembimbing_eksternal', $pembimbingEksternal->nama_pembimbing ?? '');
        $valKontakEksternal = old('kontak_pembimbing_eksternal', $pembimbingEksternal->kontak ?? '');
    @endphp

    <main id="mainContent" class="dk-page">
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('prodi.penempatan.index') }}">Mahasiswa &amp; Magang</a>
                    <span>/</span>
                    <span>Edit Penempatan</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Edit Penempatan Mahasiswa</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Perbarui informasi kegiatan, periode, dan penetapan pembimbing mahasiswa.
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

        <div class="card um-card dk-card" style="width: 100%; border-radius: 16px; overflow: visible;">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-file-pen"></i></span>
                    <span>
                        <strong>Edit Formulir Penempatan</strong>
                        <small>Perbarui data penempatan mahasiswa, mitra industri, periode, dan pembimbing terkait.</small>
                    </span>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0;">
                <form action="{{ route('prodi.penempatan.update', $penempatan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ═══ TWO-COLUMN TOP LAYOUT (Identik dengan unit/create_kerjasama.blade.php) ═══ --}}
                    <div style="display: grid; grid-template-columns: 340px 1fr; gap: 24px; padding: 24px;">

                        {{-- ══ LEFT COLUMN: Masa Berlaku (Sticky) ══ --}}
                        <div style="position: sticky; top: 24px; align-self: start;">
                            <div
                                style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible;">
                                <div x-data="{ showMasaBerlaku: true }">
                                    {{-- Card Header --}}
                                    <div @click="showMasaBerlaku = !showMasaBerlaku"
                                        style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                        <div
                                            style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(5,150,105,0.25);">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <h4
                                                style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
                                                Masa Berlaku
                                            </h4>
                                        </div>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 10px; color: var(--text-sub); transition: transform 0.3s ease;"
                                            :style="showMasaBerlaku ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>

                                    {{-- Card Body --}}
                                    <div x-show="showMasaBerlaku" x-collapse.duration.300ms style="padding: 18px;">

                                        {{-- ── Periode Mulai ── --}}
                                        <div style="margin-bottom: 16px;">
                                            <div class="mc-group">
                                                <label class="mc-label">Periode Mulai <span class="mc-req">*</span></label>
                                                <div class="mc-input-wrap">
                                                    <i class="fas fa-calendar-plus mc-icon-left"
                                                        style="color: #059669;"></i>
                                                    <input type="date" name="periode_mulai"
                                                        value="{{ $valMulai }}" required
                                                        class="mc-input" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── Periode Selesai ── --}}
                                        <div style="margin-bottom: 16px;">
                                            <div class="mc-group">
                                                <label class="mc-label">Periode Selesai</label>
                                                <div class="mc-input-wrap">
                                                    <i class="fas fa-calendar-check mc-icon-left"
                                                        style="color: #4f46e5;"></i>
                                                    <input type="date" name="periode_selesai"
                                                        value="{{ $valSelesai }}" class="mc-input" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Divider --}}
                                        <div
                                            style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 18px 0;">
                                        </div>

                                        {{-- Panduan Singkat --}}
                                        <div
                                            style="background: rgba(79,70,229,0.04); border: 1px dashed rgba(79,70,229,0.25); border-radius: 12px; padding: 14px;">
                                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                                <i class="fas fa-circle-info"
                                                    style="color: #4f46e5; margin-top: 2px; font-size: 14px;"></i>
                                                <div style="font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                                    Pastikan mahasiswa, mitra industri, dan dosen pembimbing telah terdaftar
                                                    aktif pada pangkalan data sistem.
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ RIGHT COLUMN: Form Utama ══ --}}
                        <div>
                            <div class="mc-body" style="padding: 0;">

                                {{-- ── SEKSI 1: MAHASISWA, KEGIATAN & MITRA ── --}}
                                <div style="margin-bottom: 24px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                        <div
                                            style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #4f46e5, #818cf8);">
                                        </div>
                                        <span
                                            style="font-weight: 700; font-size: 14px; color: var(--text); letter-spacing: 0.01em;">
                                            Data Penempatan &amp; Mitra Industri
                                        </span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                                        {{-- 1. Mahasiswa (Searchable Alpine Dropdown) --}}
                                        <div class="mc-group" x-data="{
                                            open: false,
                                            search: '',
                                            selectedId: '{{ $valMhsId }}',
                                            items: [
                                                @foreach($mahasiswas as $mhs)
                                                    { id: '{{ $mhs->id }}', name: '{{ addslashes($mhs->nama) }}', nim: '{{ addslashes($mhs->nim) }}' },
                                                @endforeach
                                            ],
                                            get filteredItems() {
                                                if (!this.search) return this.items;
                                                const q = this.search.toLowerCase();
                                                return this.items.filter(i => i.name.toLowerCase().includes(q) || i.nim.toLowerCase().includes(q));
                                            },
                                            get selectedItem() {
                                                return this.items.find(i => String(i.id) === String(this.selectedId));
                                            }
                                        }">
                                            <label class="mc-label">Pilih Mahasiswa <span class="mc-req">*</span></label>
                                            <input type="hidden" name="mahasiswa_id" :value="selectedId" required>

                                            <div class="alpine-dropdown" @click.outside="open = false"
                                                style="position: relative; width: 100%;">
                                                <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                                    style="min-height: 44px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                        <div
                                                            style="width: 28px; height: 28px; border-radius: 6px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                            <i class="fas fa-user-graduate"></i>
                                                        </div>
                                                        <div
                                                            style="display: flex; flex-direction: column; min-width: 0; line-height: 1.2;">
                                                            <span x-show="selectedItem"
                                                                x-text="selectedItem ? selectedItem.name : ''"
                                                                style="font-weight: 700; font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                                                            <small x-show="selectedItem"
                                                                x-text="selectedItem ? 'NIM: ' + selectedItem.nim : ''"
                                                                style="font-size: 11px; color: var(--text-sub);"></small>
                                                            <span x-show="!selectedItem"
                                                                style="color: #9ca3af; font-size: 13px;">— Pilih Mahasiswa
                                                                —</span>
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-chevron-down"
                                                        style="font-size: 10px; color: #9ca3af; transition: 0.3s;"
                                                        :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                                </div>

                                                <div class="ad-menu" x-show="open" x-transition
                                                    style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 120; max-height: 250px; overflow-y: auto; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
                                                    <div
                                                        style="padding: 8px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                        <input type="text" x-model="search"
                                                            placeholder="Cari nama atau NIM..."
                                                            style="width: 100%; padding: 8px 12px; font-size: 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface2); color: var(--text);"
                                                            @click.stop>
                                                    </div>
                                                    <template x-for="item in filteredItems" :key="item.id">
                                                        <div class="ad-item"
                                                            :class="{'selected': String(selectedId) === String(item.id)}"
                                                            @click="selectedId = item.id; open = false;"
                                                            style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer;">
                                                            <div>
                                                                <strong x-text="item.name"
                                                                    style="display: block; font-size: 13px; color: var(--text);"></strong>
                                                                <small x-text="'NIM: ' + item.nim"
                                                                    style="color: var(--text-sub); font-size: 11px;"></small>
                                                            </div>
                                                            <i class="fas fa-check" style="color: #4f46e5; font-size: 11px;"
                                                                x-show="String(selectedId) === String(item.id)"></i>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItems.length === 0"
                                                        style="padding: 12px; text-align: center; color: var(--text-sub); font-size: 12px;">
                                                        Mahasiswa tidak ditemukan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Kegiatan Kerja Sama (Searchable Alpine Dropdown) --}}
                                        <div class="mc-group" x-data="{
                                            open: false,
                                            search: '',
                                            selectedId: '{{ $valKegiatanId }}',
                                            items: [
                                                @foreach($kegiatans as $keg)
                                                    { id: '{{ $keg->id }}', name: '{{ addslashes($keg->nama_kegiatan) }}', jenis: '{{ addslashes($keg->jenis ?? 'Kerjasama') }}' },
                                                @endforeach
                                            ],
                                            get filteredItems() {
                                                if (!this.search) return this.items;
                                                const q = this.search.toLowerCase();
                                                return this.items.filter(i => i.name.toLowerCase().includes(q) || i.jenis.toLowerCase().includes(q));
                                            },
                                            get selectedItem() {
                                                return this.items.find(i => String(i.id) === String(this.selectedId));
                                            }
                                        }">
                                            <label class="mc-label">Kegiatan Kerja Sama <span
                                                    class="mc-req">*</span></label>
                                            <input type="hidden" name="kegiatan_id" :value="selectedId" required>

                                            <div class="alpine-dropdown" @click.outside="open = false"
                                                style="position: relative; width: 100%;">
                                                <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                                    style="min-height: 44px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                        <div
                                                            style="width: 28px; height: 28px; border-radius: 6px; background: rgba(5,150,105,0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                            <i class="fas fa-file-contract"></i>
                                                        </div>
                                                        <div
                                                            style="display: flex; flex-direction: column; min-width: 0; line-height: 1.2;">
                                                            <span x-show="selectedItem"
                                                                x-text="selectedItem ? selectedItem.name : ''"
                                                                style="font-weight: 700; font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                                                            <small x-show="selectedItem"
                                                                x-text="selectedItem ? 'Jenis: ' + selectedItem.jenis : ''"
                                                                style="font-size: 11px; color: var(--text-sub);"></small>
                                                            <span x-show="!selectedItem"
                                                                style="color: #9ca3af; font-size: 13px;">— Pilih Kegiatan
                                                                —</span>
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-chevron-down"
                                                        style="font-size: 10px; color: #9ca3af; transition: 0.3s;"
                                                        :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                                </div>

                                                <div class="ad-menu" x-show="open" x-transition
                                                    style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 120; max-height: 250px; overflow-y: auto; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
                                                    <div
                                                        style="padding: 8px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                        <input type="text" x-model="search" placeholder="Cari kegiatan..."
                                                            style="width: 100%; padding: 8px 12px; font-size: 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface2); color: var(--text);"
                                                            @click.stop>
                                                    </div>
                                                    <template x-for="item in filteredItems" :key="item.id">
                                                        <div class="ad-item"
                                                            :class="{'selected': String(selectedId) === String(item.id)}"
                                                            @click="selectedId = item.id; open = false;"
                                                            style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer;">
                                                            <div>
                                                                <strong x-text="item.name"
                                                                    style="display: block; font-size: 13px; color: var(--text);"></strong>
                                                                <small x-text="'Jenis: ' + item.jenis"
                                                                    style="color: var(--text-sub); font-size: 11px;"></small>
                                                            </div>
                                                            <i class="fas fa-check" style="color: #4f46e5; font-size: 11px;"
                                                                x-show="String(selectedId) === String(item.id)"></i>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItems.length === 0"
                                                        style="padding: 12px; text-align: center; color: var(--text-sub); font-size: 12px;">
                                                        Kegiatan tidak ditemukan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3. Mitra Penempatan (DUDIKA) --}}
                                        <div class="mc-group" style="grid-column: span 2;" x-data="{
                                            open: false,
                                            search: '',
                                            selectedId: '{{ $valMitraId }}',
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
                                            <label class="mc-label">Mitra Industri (DUDIKA) <span
                                                    class="mc-req">*</span></label>
                                            <input type="hidden" name="mitra_id" :value="selectedId" required>

                                            <div class="alpine-dropdown" @click.outside="open = false"
                                                style="position: relative; width: 100%;">
                                                <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                                    style="min-height: 44px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                        <div
                                                            style="width: 28px; height: 28px; border-radius: 6px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                        <span x-show="selectedItem"
                                                            x-text="selectedItem ? selectedItem.name : ''"
                                                            style="font-weight: 700; font-size: 13px; color: var(--text);"></span>
                                                        <span x-show="!selectedItem"
                                                            style="color: #9ca3af; font-size: 13px;">— Pilih Mitra Industri
                                                            —</span>
                                                    </div>
                                                    <i class="fas fa-chevron-down"
                                                        style="font-size: 10px; color: #9ca3af; transition: 0.3s;"
                                                        :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                                </div>

                                                <div class="ad-menu" x-show="open" x-transition
                                                    style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 120; max-height: 250px; overflow-y: auto; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
                                                    <div
                                                        style="padding: 8px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                        <input type="text" x-model="search"
                                                            placeholder="Cari nama mitra industri..."
                                                            style="width: 100%; padding: 8px 12px; font-size: 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface2); color: var(--text);"
                                                            @click.stop>
                                                    </div>
                                                    <template x-for="item in filteredItems" :key="item.id">
                                                        <div class="ad-item"
                                                            :class="{'selected': String(selectedId) === String(item.id)}"
                                                            @click="selectedId = item.id; open = false;"
                                                            style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer;">
                                                            <strong x-text="item.name"
                                                                style="font-size: 13px; color: var(--text);"></strong>
                                                            <i class="fas fa-check" style="color: #4f46e5; font-size: 11px;"
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
                                    </div>
                                </div>

                                {{-- ── SEKSI 2: PENETAPAN PEMBIMBING ── --}}
                                <div
                                    style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 22px; margin-bottom: 24px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
                                        <div
                                            style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="fas fa-users-rectangle"></i>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text);">
                                                Penetapan Dosen &amp; Pembimbing Industri
                                            </h4>
                                            <small style="color: var(--text-sub); font-size: 12px;">Tentukan dosen
                                                pembimbing internal Polimdo dan pembimbing lapangan dari instansi
                                                mitra.</small>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        {{-- Pembimbing Internal (Dosen) --}}
                                        <div
                                            style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px;">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                                <i class="fas fa-chalkboard-user"
                                                    style="color: #4f46e5; font-size: 13px;"></i>
                                                <span
                                                    style="font-weight: 700; font-size: 13px; color: var(--text);">Pembimbing
                                                    Internal (Dosen)</span>
                                            </div>

                                            <div class="mc-group" style="margin-bottom: 12px;">
                                                <label class="mc-label">Nama Dosen Pembimbing <span
                                                        class="mc-req">*</span></label>
                                                <div class="mc-input-wrap">
                                                    <i class="fas fa-user-tie mc-icon-left"></i>
                                                    <input type="text" name="nama_pembimbing_internal"
                                                        value="{{ $valNamaInternal }}" required
                                                        placeholder="Contoh: Dr. Ir. Nama Dosen, M.T." class="mc-input">
                                                </div>
                                            </div>

                                            <div class="mc-group">
                                                <label class="mc-label">Kontak / No. WhatsApp Dosen</label>
                                                <div class="mc-input-wrap">
                                                    <i class="fab fa-whatsapp mc-icon-left" style="color: #10b981;"></i>
                                                    <input type="text" name="kontak_pembimbing_internal"
                                                        value="{{ $valKontakInternal }}"
                                                        placeholder="08xxxxxxxxxx" class="mc-input">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Pembimbing Eksternal (Mitra) --}}
                                        <div
                                            style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px;">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                                <i class="fas fa-building-user"
                                                    style="color: #059669; font-size: 13px;"></i>
                                                <span
                                                    style="font-weight: 700; font-size: 13px; color: var(--text);">Pembimbing
                                                    Eksternal (Mitra)</span>
                                            </div>

                                            <div class="mc-group" style="margin-bottom: 12px;">
                                                <label class="mc-label">Nama Pembimbing Mitra <span
                                                        class="mc-req">*</span></label>
                                                <div class="mc-input-wrap">
                                                    <i class="fas fa-user-check mc-icon-left"></i>
                                                    <input type="text" name="nama_pembimbing_eksternal"
                                                        value="{{ $valNamaEksternal }}" required
                                                        placeholder="Nama Pembimbing di Instansi/Mitra" class="mc-input">
                                                </div>
                                            </div>

                                            <div class="mc-group">
                                                <label class="mc-label">Kontak / Email Pembimbing Mitra</label>
                                                <div class="mc-input-wrap">
                                                    <i class="fas fa-envelope mc-icon-left" style="color: #6366f1;"></i>
                                                    <input type="text" name="kontak_pembimbing_eksternal"
                                                        value="{{ $valKontakEksternal }}"
                                                        placeholder="Email / No. Telp" class="mc-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── FOOTER ACTIONS ── --}}
                                <div
                                    style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border);">
                                    <a href="{{ route('prodi.penempatan.index') }}" class="rfc-btn"
                                        style="background: var(--surface2); color: var(--text); border: 1.5px solid var(--border); padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-arrow-left"></i> Batal
                                    </a>
                                    <button type="submit" class="rfc-btn rfc-btn-primary"
                                        style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-save"></i> Perbarui Penempatan
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
