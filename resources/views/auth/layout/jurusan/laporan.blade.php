<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-file-signature" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Laporan Data</span>
        </div>
        <h2>Laporan Data Kerjasama</h2>
        <p>Saring dan unduh dokumen laporan kerjasama jurusan secara kolektif.</p>
    </div>

    <!-- Filter Section (Redesigned) -->
    <div class="report-filter-container" x-data="{ showFilters: true }">
        <div class="rfc-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
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
                  data-preview-url="{{ route('jurusan.laporan.preview') }}"
                  data-pdf-url="{{ route('jurusan.laporan.pdf') }}"
                  data-excel-url="{{ route('jurusan.laporan.excel') }}">
                
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
                                    <span class="adp-month" x-text="monthNames[month] + ' ' + year"></span>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
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
                                <input type="text" name="tanggal_akhir" x-model="formattedDate" readonly @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <span class="adp-month" x-text="monthNames[month] + ' ' + year"></span>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
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
                    
                    <!-- Column 4: Status Evaluasi -->
                    <div class="rfc-group" x-data="{ 
                        open: false, 
                        selected: 'all', 
                        selectedLabel: 'Semua Status',
                        items: [
                            { id: 'all', label: 'Semua Status' },
                            { id: 'draft', label: 'Draft' },
                            { id: 'menunggu_evaluasi', label: 'Menunggu Evaluasi' },
                            <!-- { id: 'revisi', label: 'Revisi' }, -->
                            { id: 'selesai', label: 'Selesai/Layak' }
                        ]
                    }">
                        <label>Status Evaluasi</label>
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
                        <!-- State Empty (Disembunyikan, untuk dikendalikan via JS) -->
                        <tr id="emptyStateRow" style="display: none;">
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