@php
    $kriteriaList = $kriterias ?? collect();
    $totalKriteria = $kriteriaList->count();
    $totalMitraCount = $kriteriaList->sum('total_count');

    // Find the classification with the most mitras
    $mostFrequent = $kriteriaList->sortByDesc('total_count')->first();
    $mostFrequentName = $mostFrequent && $mostFrequent->total_count > 0 ? $mostFrequent->nama : '-';
    $mostFrequentCount = $mostFrequent ? $mostFrequent->total_count : 0;

    // Mapping description for each Klasifikasi ID
    $descriptions = [
        1 => 'Perusahaan besar yang beroperasi di beberapa negara dengan kantor pusat global.',
        2 => 'Perusahaan dalam negeri yang memiliki skala operasional luas dan standar kualitas tinggi.',
        3 => 'Perusahaan yang bergerak di bidang teknologi dengan jangkauan pasar internasional.',
        4 => 'Perusahaan rintisan baru yang berfokus pada inovasi dan pengembangan produk teknologi.',
        5 => 'Lembaga non-pemerintah internasional yang bergerak di bidang sosial, kemanusiaan, atau lingkungan.',
        6 => 'Lembaga internasional yang beranggotakan beberapa negara (seperti PBB, WHO, Bank Dunia).',
        7 => 'Institusi pendidikan tinggi tingkat global yang terakreditasi dalam peringkat dunia QS Top 200.',
        8 => 'Lembaga pemerintahan (kementerian, dinas) serta badan usaha milik negara/daerah.',
        9 => 'Institusi pelayanan kesehatan masyarakat baik milik pemerintah maupun swasta.',
        10 => 'Entitas bisnis komersial atau pelaku usaha skala kecil hingga menengah.',
        11 => 'Sekolah, akademi, institut, atau lembaga pelatihan pendidikan non-tinggi.',
        12 => 'Asosiasi kemasyarakatan, komunitas, atau perkumpulan profesi berbadan hukum.',
        13 => 'Perguruan tinggi mitra dalam negeri baik tingkat universitas, fakultas, maupun program studi.',
        14 => 'Pusat penelitian, laboratorium, atau lembaga kajian sains dan teknologi.',
        15 => 'Pusat kebudayaan, museum, atau galeri seni berskala nasional maupun internasional.',
    ];
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('jurusan.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Kriteria Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-hands-helping"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Kriteria Mitra</h2>
                    <p class="ud-subtitle">
                        Referensi kriteria/klasifikasi mitra industri dan institusi yang bekerjasama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-filter"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Kriteria Mitra</span>
                <div class="dk-stat-value">{{ $totalKriteria }} <span>Kategori</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary">
            <div class="dk-stat-icon"><i class="fas fa-handshake"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Mitra Terdaftar</span>
                <div class="dk-stat-value">{{ $totalMitraCount }} <span>Mitra</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success">
            <div class="dk-stat-icon"><i class="fas fa-star"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Kriteria Terbanyak</span>
                <div class="dk-stat-value"
                    style="font-size: 15px; font-weight: 700; line-height: 1.2; margin-top: 4px;">
                    {{ Str::limit($mostFrequentName, 30) }}
                    <span style="font-size: 12px; font-weight: 500; color: var(--ud-text-muted); display: block;">
                        ({{ $mostFrequentCount }} Mitra)
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Daftar Kriteria Mitra</strong>
                    <small>Referensi kriteria kualifikasi mitra kerjasama</small>
                </span>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 80px;">#</th>
                            <th class="um-th" style="width: 280px;">Klasifikasi / Kriteria Mitra</th>
                            <th class="um-th dk-col-name">Deskripsi / Keterangan</th>
                            <th class="um-th" style="text-align: center; width: 160px;">Jumlah Mitra</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kriteriaList as $kriteria)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $kriteria->nama }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <span class="dk-entity-text" style="font-size: 13.5px; color: var(--ud-text-muted); font-weight: 500; line-height: 1.4;">
                                        {{ $descriptions[$kriteria->id] ?? 'Deskripsi kriteria mitra terkait.' }}
                                    </span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $kriteria->total_count }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="4" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon"><i class="fas fa-hands-helping"></i></div>
                                        <p class="um-empty-title">Belum ada data kriteria mitra</p>
                                        <p class="um-empty-sub">Kriteria / klasifikasi mitra kerjasama akan tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
