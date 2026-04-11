<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-file-signature" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Laporan Data</span>
        </div>
        <h2>Laporan Data Kerjasama</h2>
        <p>Saring dan unduh dokumen laporan kerjasama unit kerja secara kolektif.</p>
    </div>

    <!-- Filter Section -->
    <div class="report-filter-container" x-data="{ showFilters: true }">
        <div class="rfc-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Laporan</h3>
                    <p>Saring data kerjasama secara rinci sebelum ditampilkan atau diunduh.</p>
                </div>
            </div>
            <div style="color: var(--text-sub); font-size: 16px; transition: transform 0.3s;" :style="showFilters ? 'transform: rotate(180deg)' : 'transform: rotate(0)'">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="rfc-body" x-show="showFilters" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4">
            <form id="filterForm" class="rfc-form"
                  data-preview-url="{{ route('unit.laporan.preview') }}"
                  data-pdf-url="{{ route('unit.laporan.pdf') }}"
                  data-excel-url="{{ route('unit.laporan.excel') }}">

                <div class="rfc-grid">
                    <!-- Column 1: From -->
                    <div class="rfc-group" x-data="datepicker('')">
                        <label>From</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-day mc-icon-left"></i>
                                <input type="text" name="tanggal_awal" x-model="formattedDate" readonly @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}" @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..." style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);" @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}" @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames"><div class="adp-day-name" x-text="day"></div></template>
                                    <template x-for="blankday in blanks"><div class="adp-day empty"></div></template>
                                    <template x-for="date in days">
                                        <div class="adp-day" :class="{'today': isToday(date), 'selected': isSelected(date)}" @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: To -->
                    <div class="rfc-group" x-data="datepicker('')">
                        <label>To</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-check mc-icon-left"></i>
                                <input type="text" name="tanggal_akhir" x-model="formattedDate" readonly @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}" @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..." style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);" @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}" @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames"><div class="adp-day-name" x-text="day"></div></template>
                                    <template x-for="blankday in blanks"><div class="adp-day empty"></div></template>
                                    <template x-for="date in days">
                                        <div class="adp-day" :class="{'today': isToday(date), 'selected': isSelected(date)}" @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Jenis Kerjasama -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua Jenis',
                        items: [
                            { id: 'all', label: 'Semua Jenis' },
                            @foreach($jenisKerjasama as $jenis)
                            { id: '{{ $jenis->id }}', label: '{{ $jenis->nama_kerjasama }}' },
                            @endforeach
                        ]
                    }">
                        <label>Jenis Kerjasama</label>
                        <input type="hidden" name="id_jenis" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-handshake" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{'selected': selected == item.id}"
                                         @click="selected = item.id; selectedLabel = item.label; open = false"
                                         x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Column 4: Status -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua Status',
                        items: [
                            { id: 'all', label: 'Semua Status' },
                            { id: 'draft', label: 'Draft' },
                            { id: 'menunggu_evaluasi', label: 'Menunggu Evaluasi' },
                            { id: 'menunggu_validasi', label: 'Menunggu Validasi Pimpinan' },
                            { id: 'selesai', label: 'Selesai' }
                        ]
                    }">
                        <label>Status</label>
                        <input type="hidden" name="status" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-info-circle" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{'selected': selected == item.id}"
                                         @click="selected = item.id; selectedLabel = item.label; open = false"
                                         x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rfc-footer">
                    <button type="button" id="btnTampilkan" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                    <button type="button" id="btnCetakPdf" class="rfc-btn rfc-btn-danger">
                        <i class="fas fa-file-pdf"></i> Cetak PDF
                    </button>
                    <button type="button" id="btnExportExcel" class="rfc-btn rfc-btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Table Section -->
    <div class="card um-card">
        <div class="card-header um-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="card-title" style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-eye" style="color: var(--accent);"></i>
                <span>Preview Data</span>
                <span id="previewCount" style="display:none; font-size: 12px; font-weight: 600; color: var(--accent); background: rgba(79,70,229,.1); padding: 3px 10px; border-radius: 20px;"></span>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap" style="overflow-x: auto;">
                <table id="previewTable" class="um-table" style="white-space: nowrap; min-width: 1000px;">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">No</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Jenis Kerjasama</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Periode</th>
                            <th class="um-th">Status</th>
                        </tr>
                    </thead>
                    <tbody id="previewBody">
                        <tr id="emptyStateRow">
                            <td colspan="6" class="um-empty">
                                <div class="um-empty-state" style="padding: 30px 0;">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-search" style="font-size: 28px; opacity: 0.3; color: var(--text-sub);"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada preview data</p>
                                    <p class="um-empty-sub">Silakan klik <strong>Tampilkan</strong> untuk melihat hasil filter.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const previewBody = document.getElementById('previewBody');
    const emptyRow = document.getElementById('emptyStateRow');
    const previewCount = document.getElementById('previewCount');

    function getFormParams() {
        const fd = new FormData(form);
        const params = new URLSearchParams();
        fd.forEach(function (val, key) { if (val) params.append(key, val); });
        return params.toString();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    // Tampilkan preview
    document.getElementById('btnTampilkan').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';

        var url = form.dataset.previewUrl + '?' + getFormParams();

        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                previewBody.innerHTML = '';

                if (!data || data.length === 0) {
                    previewBody.innerHTML = '<tr><td colspan="6" class="um-empty"><div class="um-empty-state" style="padding:30px 0;"><div class="um-empty-icon"><i class="fas fa-inbox" style="font-size:28px; opacity:0.3; color:var(--text-sub);"></i></div><p class="um-empty-title">Tidak ada data ditemukan</p><p class="um-empty-sub">Coba ubah filter untuk menampilkan data lain.</p></div></td></tr>';
                    previewCount.style.display = 'none';
                } else {
                    previewCount.textContent = data.length + ' data';
                    previewCount.style.display = 'inline-block';

                    data.forEach(function (item, idx) {
                        var jenis = (item.jenis_kerjasama || []).map(function (j) { return j.nama_kerjasama; }).join(', ') || '-';
                        var mitra = (item.mitras || []).map(function (m) { return m.nama_mitra; }).join(', ') || '-';
                        var periode = formatDate(item.periode_mulai) + ' — ' + formatDate(item.periode_selesai);
                        var sLabel = item.status_label || item.status || '-';
                        var sClass = item.status_class || 'tag-orange';

                        var tr = document.createElement('tr');
                        tr.className = 'um-row';
                        tr.innerHTML =
                            '<td class="um-td um-td-num"><span class="um-num">' + String(idx + 1).padStart(3, '0') + '</span></td>' +
                            '<td class="um-td"><span class="um-name">' + (item.nama_kegiatan || '-') + '</span></td>' +
                            '<td class="um-td"><span class="tag tag-purple" style="font-size:11px;">' + jenis + '</span></td>' +
                            '<td class="um-td" style="font-size:12px;">' + mitra + '</td>' +
                            '<td class="um-td" style="font-size:12px; color:var(--text-sub);">' + periode + '</td>' +
                            '<td class="um-td"><span class="tag ' + sClass + '" style="font-size:11px;"><i class="fas fa-circle" style="font-size:6px;"></i> ' + sLabel + '</span></td>';
                        previewBody.appendChild(tr);
                    });
                }
            })
            .catch(function (err) {
                console.error(err);
                previewBody.innerHTML = '<tr><td colspan="6" class="um-empty"><div class="um-empty-state" style="padding:30px 0;"><p class="um-empty-title" style="color:#ef4444;">Gagal memuat data</p><p class="um-empty-sub">Terjadi kesalahan. Silakan coba lagi.</p></div></td></tr>';
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-search"></i> Tampilkan';
            });
    });

    // Cetak PDF
    document.getElementById('btnCetakPdf').addEventListener('click', function () {
        window.open(form.dataset.pdfUrl + '?' + getFormParams(), '_blank');
    });

    // Export Excel
    document.getElementById('btnExportExcel').addEventListener('click', function () {
        window.open(form.dataset.excelUrl + '?' + getFormParams(), '_blank');
    });
});
</script>