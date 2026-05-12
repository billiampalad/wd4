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
        <div class="rfc-header"
            style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
            @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Laporan</h3>
                    <p>Saring data kerjasama secara rinci sebelum ditampilkan atau diunduh.</p>
                </div>
            </div>
            <div style="color: var(--text-sub); font-size: 16px; transition: transform 0.3s;"
                :style="showFilters ? 'transform: rotate(180deg)' : 'transform: rotate(0)'">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="rfc-body" x-show="showFilters" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4">
            <form id="filterForm" class="rfc-form" data-preview-url="{{ route('pimpinan.laporan.preview') }}"
                data-pdf-url="{{ route('pimpinan.laporan.pdf') }}" data-excel-url="{{ route('pimpinan.laporan.excel') }}">

                <div class="rfc-grid">
                    <!-- Column 1: From -->
                    <div class="rfc-group" x-data="datepicker('')">
                        <label>From</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-day mc-icon-left"></i>
                                <input type="text" name="tanggal_awal" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()"
                                            x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i
                                                class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i
                                                class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames">
                                        <div class="adp-day-name" x-text="day"></div>
                                    </template>
                                    <template x-for="blankday in blanks">
                                        <div class="adp-day empty"></div>
                                    </template>
                                    <template x-for="date in days">
                                        <div class="adp-day"
                                            :class="{'today': isToday(date), 'selected': isSelected(date)}"
                                            @click="selectDate(date)" x-text="date"></div>
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
                                <input type="text" name="tanggal_akhir" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()"
                                            x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i
                                                class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i
                                                class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames">
                                        <div class="adp-day-name" x-text="day"></div>
                                    </template>
                                    <template x-for="blankday in blanks">
                                        <div class="adp-day empty"></div>
                                    </template>
                                    <template x-for="date in days">
                                        <div class="adp-day"
                                            :class="{'today': isToday(date), 'selected': isSelected(date)}"
                                            @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Unit Pelaksana -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua Unit',
                        items: [
                            { id: 'all',     label: 'Semua Unit' },
                            { id: 'jurusan', label: 'Jurusan' },
                            { id: 'upa',     label: 'UPA' },
                            { id: 'pusat',   label: 'Pusat' }
                        ]
                    }">
                        <label>Unit Pelaksana</label>
                        <input type="hidden" name="tipe_pelaksana" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-sitemap" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
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

                    <!-- Column 4: Jurusan -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua Jurusan',
                        items: @js(($jurusans ?? collect())->map(fn($jurusan) => ['id' => $jurusan->id, 'label' => $jurusan->nama_jurusan])->prepend(['id' => 'all', 'label' => 'Semua Jurusan'])->values())
                    }">
                        <label>Jurusan</label>
                        <input type="hidden" name="jurusan_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-microchip" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
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

                    <!-- Column 5: UPA -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua UPA',
                        items: @js(($upas ?? collect())->map(fn($upa) => ['id' => $upa->id, 'label' => $upa->nama_upa])->prepend(['id' => 'all', 'label' => 'Semua UPA'])->values())
                    }">
                        <label>UPA</label>
                        <input type="hidden" name="upa_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-building-columns" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
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

                    <!-- Column 6: Pusat -->
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        selectedLabel: 'Semua Pusat',
                        items: @js(($pusats ?? collect())->map(fn($pusat) => ['id' => $pusat->id, 'label' => $pusat->nama_pusat])->prepend(['id' => 'all', 'label' => 'Semua Pusat'])->values())
                    }">
                        <label>Pusat</label>
                        <input type="hidden" name="pusat_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-landmark" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
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
                            { id: 'all',               label: 'Semua Status' },
                            { id: 'aktif',             label: 'Aktif' },
                            { id: 'proses',            label: 'Proses' },
                            { id: 'dalam perpanjangan',label: 'Dalam Perpanjangan' },
                            { id: 'kadarluarsa',       label: 'Kadarluarsa' },
                            { id: 'tidak aktif',       label: 'Tidak Aktif' }
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
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
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
        <div class="card-header um-header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="card-title"
                style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-eye" style="color: var(--accent);"></i>
                <span>Preview Data</span>
                <span id="previewCount"
                    style="display:none; font-size: 12px; font-weight: 600; color: var(--accent); background: rgba(79,70,229,.1); padding: 3px 10px; border-radius: 20px;"></span>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap" style="overflow-x: auto;">
                <table id="previewTable" class="um-table" style="white-space: nowrap; min-width: 1000px;">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th">Status</th>
                        </tr>
                    </thead>
                    <tbody id="previewBody">
                        <tr id="idleRow">
                            <td colspan="6" class="um-empty">
                                <div class="um-empty-state" style="padding:30px 0;">
                                    <div class="um-empty-icon"><i class="fas fa-filter"
                                            style="font-size:28px; opacity:0.3; color:var(--text-sub);"></i></div>
                                    <p class="um-empty-title">Belum ada data ditampilkan</p>
                                    <p class="um-empty-sub">Gunakan filter di atas lalu klik <strong>Tampilkan</strong>
                                        untuk memuat data.</p>
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
        var form = document.getElementById('filterForm');
        var previewBody = document.getElementById('previewBody');
        var previewCount = document.getElementById('previewCount');
        var btnTampilkan = document.getElementById('btnTampilkan');

        function getFormParams() {
            var fd = new FormData(form);
            var params = new URLSearchParams();
            // Lewati nilai kosong dan sentinel 'all' agar tidak dikirim ke backend
            fd.forEach(function (val, key) {
                if (val && val !== 'all') params.append(key, val);
            });
            return params.toString();
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            try {
                var str = String(dateStr).split('T')[0];
                var d = new Date(str);
                if (isNaN(d.getTime())) return String(dateStr);
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                var day = String(d.getDate()).padStart(2, '0');
                return day + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
            } catch (e) {
                return '-';
            }
        }

        function showLoading() {
            previewBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 40px 0;"><i class="fas fa-spinner fa-spin" style="font-size: 20px; color: var(--accent); opacity: 0.6;"></i><p style="margin-top: 10px; font-size: 12px; color: var(--text-sub);">Memuat data kerjasama...</p></td></tr>';
        }

        function showEmpty() {
            previewBody.innerHTML = '<tr><td colspan="6" class="um-empty"><div class="um-empty-state" style="padding:30px 0;"><div class="um-empty-icon"><i class="fas fa-inbox" style="font-size:28px; opacity:0.3; color:var(--text-sub);"></i></div><p class="um-empty-title">Tidak ada data ditemukan</p><p class="um-empty-sub">Coba ubah filter untuk menampilkan data lain.</p></div></td></tr>';
            previewCount.style.display = 'none';
        }

        function showError() {
            previewBody.innerHTML = '<tr><td colspan="6" class="um-empty"><div class="um-empty-state" style="padding:30px 0;"><p class="um-empty-title" style="color:#ef4444;">Gagal memuat data</p><p class="um-empty-sub">Terjadi kesalahan. Silakan coba lagi.</p></div></td></tr>';
        }

        function buildRow(item, idx) {
            var title = item.title || '-';
            var docNumber = item.doc_number || '';
            var jenis = item.jenis || '-';
            var mitraName = (item.mitra && item.mitra.nama_mitra) ? item.mitra.nama_mitra : '-';

            var pelaksanaIcon = 'fa-building';
            var pelaksanaClass = 'dk-entity-indigo';
            var pelaksanaName = '-';
            if (item.tipe_pelaksana === 'jurusan') {
                pelaksanaIcon = 'fa-microchip';
                pelaksanaName = (item.jurusan && item.jurusan.nama_jurusan) ? item.jurusan.nama_jurusan : '-';
            } else if (item.tipe_pelaksana === 'upa') {
                pelaksanaIcon = 'fa-building-columns';
                pelaksanaClass = 'dk-entity-cyan';
                pelaksanaName = (item.upa && item.upa.nama_upa) ? item.upa.nama_upa : '-';
            } else if (item.tipe_pelaksana === 'pusat') {
                pelaksanaIcon = 'fa-landmark';
                pelaksanaClass = 'dk-entity-violet';
                pelaksanaName = (item.pusat && item.pusat.nama_pusat) ? item.pusat.nama_pusat : '-';
            }

            var mulai = formatDate(item.start_date);
            var selesai = formatDate(item.end_date);

            var status = (item.status || '').toLowerCase();
            var isExpired = ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'].indexOf(status) !== -1;
            var isExtended = status.indexOf('perpanjangan') !== -1;

            var statusClass = 'dk-status-neutral';
            var statusIcon = 'fa-circle-info';
            var statusLabel = 'Belum Diatur';

            if (status === 'aktif') {
                statusClass = 'dk-status-active';
                statusIcon = 'fa-circle-check';
                statusLabel = 'Aktif';
            } else if (status === 'proses' || status === 'menunggu_validasi') {
                statusClass = 'dk-status-info';
                statusIcon = 'fa-spinner fa-spin';
                statusLabel = status === 'proses' ? 'Proses' : 'Menunggu Validasi';
            } else if (isExtended) {
                statusClass = 'dk-status-warning';
                statusIcon = 'fa-clock';
                statusLabel = 'Perpanjangan';
            } else if (isExpired) {
                statusClass = 'dk-status-danger';
                statusIcon = 'fa-circle-xmark';
                statusLabel = 'Kadarluarsa';
            } else if (status === 'tidak aktif') {
                statusClass = 'dk-status-muted';
                statusIcon = 'fa-circle-minus';
                statusLabel = 'Tidak Aktif';
            } else if (status !== '') {
                statusLabel = status.replace(/\b\w/g, function (l) { return l.toUpperCase(); });
            }

            var tr = document.createElement('tr');
            tr.className = 'um-row dk-row';
            tr.innerHTML =
                '<td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">' +
                '<span class="um-num dk-num">' + String(idx + 1).padStart(2, '0') + '</span>' +
                '</td>' +
                '<td class="um-td dk-title-cell" style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;">' +
                '<div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">' +
                '<span class="dk-doc-number">#' + (docNumber || '-') + '</span>' +
                '<span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">' + title + '</span>' +
                '<span class="dk-doc-kind">' + jenis + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;">' +
                '<div class="dk-entity" style="align-items: flex-start;">' +
                '<span class="dk-entity-icon ' + pelaksanaClass + '" style="flex-shrink: 0;">' +
                '<i class="fas ' + pelaksanaIcon + '"></i>' +
                '</span>' +
                '<span class="dk-entity-text" style="padding-top: 4px;">' + pelaksanaName + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;">' +
                '<div class="dk-entity" style="align-items: flex-start;">' +
                '<span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">' +
                '<i class="fas fa-building"></i>' +
                '</span>' +
                '<span class="dk-entity-text" style="padding-top: 4px;">' + mitraName + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">' +
                '<div class="dk-date-range-compact">' +
                '<span class="date-val">' + mulai + '</span>' +
                '<span class="date-sep">s/d</span>' +
                '<span class="date-val">' + selesai + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;">' +
                '<span class="dk-status ' + statusClass + '">' +
                '<i class="fas ' + statusIcon + '"></i> ' +
                statusLabel +
                '</span>' +
                '</td>';
            return tr;
        }

        // Core function to fetch and render data
        function loadData() {
            // Immediately show loading skeleton (clears any previous content)
            showLoading();

            btnTampilkan.disabled = true;
            btnTampilkan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';

            var url = form.dataset.previewUrl + '?' + getFormParams();

            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data || data.length === 0) {
                        showEmpty();
                        return;
                    }

                    previewCount.textContent = data.length + ' data';
                    previewCount.style.display = 'inline-block';

                    // Build all rows in a DocumentFragment first, then insert once
                    // to avoid the brief empty-tbody flash caused by innerHTML='' + per-row appendChild
                    var fragment = document.createDocumentFragment();
                    data.forEach(function (item, idx) {
                        try {
                            fragment.appendChild(buildRow(item, idx));
                        } catch (e) {
                            console.error('Error rendering row ' + idx + ':', e);
                        }
                    });
                    previewBody.innerHTML = '';
                    previewBody.appendChild(fragment);
                })
                .catch(function (err) {
                    console.error(err);
                    showError();
                })
                .finally(function () {
                    btnTampilkan.disabled = false;
                    btnTampilkan.innerHTML = '<i class="fas fa-search"></i> Tampilkan';
                });
        }

        // Button click handler
        btnTampilkan.addEventListener('click', loadData);

        // Cetak PDF
        document.getElementById('btnCetakPdf').addEventListener('click', function () {
            window.open(form.dataset.pdfUrl + '?' + getFormParams(), '_blank');
        });

        // Export Excel
        document.getElementById('btnExportExcel').addEventListener('click', function () {
            window.open(form.dataset.excelUrl + '?' + getFormParams(), '_blank');
        });

        // Auto-load data on page load (call directly, no click simulation)
        loadData();
    });
</script>
