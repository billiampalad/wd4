<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span>Kerjasama</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Form Laporan</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff;">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Dokumentasi Kerjasama</span>
                    <h2 id="pageTitle">Form Laporan</h2>
                    <p id="pageDesc">
                        Upload dan kelola dokumen laporan kerjasama Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
    <div class="dk-alert dk-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="dk-alert dk-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div style="width: 100%; max-width: 1000px; margin: 0 auto; padding: 0 24px;">

        {{-- ═══ Upload Card ═══ --}}
        <div class="card um-card dk-card" style="overflow: visible; margin-bottom: 32px;">
            {{-- Card Header --}}
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff;">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </span>
                    <span>
                        <strong>Upload Laporan</strong>
                        <small>Unggah dokumen laporan kerjasama (PDF / Word) untuk dokumentasi dan arsip</small>
                    </span>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="card-body dk-card-body" style="padding: 28px;" x-data="laporanUploader()">
                <form action="{{ route('unit.form.store') }}" method="POST" enctype="multipart/form-data" @submit="submitting = true">
                    @csrf


                    {{-- ─── Drag & Drop Zone ─── --}}
                    <div style="margin-bottom: 28px;">
                        <label class="mc-label" style="margin-bottom: 10px;">File Dokumen <span class="mc-req">*</span></label>

                        {{-- Drop Zone --}}
                        <div
                            @dragover.prevent="dragover = true"
                            @dragleave.prevent="dragover = false"
                            @drop.prevent="handleDrop($event)"
                            @click="$refs.fileInput.click()"
                            :style="dragover
                                ? 'border-color: #4f46e5; background: rgba(79,70,229,0.06); transform: scale(1.005);'
                                : (fileName
                                    ? 'border-color: #10b981; background: rgba(16,185,129,0.04);'
                                    : '')"
                            style="position: relative; border: 2px dashed var(--border); border-radius: 16px; padding: 40px 24px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: var(--surface2);">
                            <input type="file" name="file_laporan" accept=".pdf,.doc,.docx" x-ref="fileInput" @change="handleFile($event)" style="display: none;" required />

                            {{-- Empty State --}}
                            <div x-show="!fileName" style="pointer-events: none;">
                                <div style="width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.08)); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-arrow-up" style="font-size: 28px; color: #4f46e5;"></i>
                                </div>
                                <p style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--text);">
                                    Drag & drop file di sini
                                </p>
                                <p style="margin: 0 0 14px; font-size: 12px; color: var(--text-sub);">
                                    atau klik untuk memilih file (PDF / Word)
                                </p>
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; font-size: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(79,70,229,0.3); pointer-events: none;">
                                    <i class="fas fa-folder-open"></i>
                                    Pilih File
                                </div>
                                <p style="margin: 12px 0 0; font-size: 11px; color: var(--text-sub);">
                                    <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                    Maksimum 3 MB — PDF, DOC, DOCX
                                </p>
                            </div>

                            {{-- File Selected State --}}
                            <div x-show="fileName" x-cloak style="pointer-events: none;">
                                <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
                                    {{-- File Icon --}}
                                    <div :style="fileType === 'pdf' ? 'background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 14px rgba(239,68,68,0.3);' : 'background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 14px rgba(37,99,235,0.3);'"
                                        style="width: 56px; height: 56px; border-radius: 14px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                                        <i :class="fileType === 'pdf' ? 'fas fa-file-pdf' : 'fas fa-file-word'"></i>
                                    </div>
                                    {{-- File Info --}}
                                    <div style="text-align: left;">
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: var(--text); max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="fileName"></p>
                                        <p style="margin: 4px 0 0; font-size: 12px; color: var(--text-sub);" x-text="fileSize"></p>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
                                            <span style="font-size: 11px; font-weight: 600; color: #10b981;">Siap diupload</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Remove File Button --}}
                        <div x-show="fileName" x-cloak style="margin-top: 10px; text-align: center;">
                            <button type="button" @click="removeFile()"
                                style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; padding: 8px 20px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;"
                                onmouseover="this.style.background='rgba(239,68,68,0.15)'"
                                onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                                <i class="fas fa-trash-alt"></i> Hapus File
                            </button>
                        </div>

                        @error('file_laporan')
                        <span style="color: #ef4444; font-size: 11px; margin-top: 8px; display: block;">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </span>
                        @enderror
                    </div>

                    {{-- ─── Submit Buttons ─── --}}
                    <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 24px; border-top: 1px solid var(--border); margin-top: 12px;">
                        <a href="{{ route('unit.dkerjasama') }}" class="dk-btn-icon"
                            style="text-decoration: none; background: var(--surface2); color: var(--text); border: 1px solid var(--border); padding: 12px 24px; border-radius: 12px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; width: auto; height: auto;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" :disabled="submitting || !fileName"
                            class="dk-primary-btn"
                            style="padding: 12px 28px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none;"
                            :style="(!fileName || submitting) ? 'opacity: 0.5; cursor: not-allowed; filter: grayscale(1);' : ''">
                            <template x-if="!submitting">
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-cloud-arrow-up"></i> Upload Laporan
                                </span>
                            </template>
                            <template x-if="submitting">
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-spinner fa-spin"></i> Mengupload...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══ Uploaded Files Table ═══ --}}
        <div class="card um-card dk-card">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon" style="background: linear-gradient(135deg, #059669, #10b981); color: #fff;">
                        <i class="fas fa-folder-open"></i>
                    </span>
                    <span>
                        <strong>Riwayat Laporan</strong>
                        <small>{{ isset($laporanFiles) ? $laporanFiles->count() : 0 }} dokumen telah diunggah</small>
                    </span>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0;">
                @if(isset($laporanFiles) && $laporanFiles->count() > 0)
                <div class="table-wrap um-table-wrap dk-table-wrap">
                    <table class="um-table dk-table">
                        <thead>
                            <tr>
                                <th class="um-th um-th-num">#</th>
                                <th class="um-th">Nama File</th>
                                <th class="um-th">Tanggal Upload</th>
                                <th class="um-th um-th-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporanFiles as $index => $file)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num dk-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @php
                                        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($extension) === 'pdf';
                                        $iconClass = $isPdf ? 'fa-file-pdf' : 'fa-file-word';
                                        $iconBg = $isPdf ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #2563eb, #1d4ed8)';
                                        $iconShadow = $isPdf ? 'rgba(239,68,68,0.2)' : 'rgba(37,99,235,0.2)';
                                        @endphp
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $iconBg }}; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 4px 10px {{ $iconShadow }};">
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $file->original_name }}</p>
                                            <span style="font-size: 11px; color: var(--text-sub);">{{ strtoupper($extension) }} Document</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td">
                                    <span style="font-size: 12px; color: var(--text-sub); font-weight: 500;">
                                        <i class="fas fa-calendar-alt" style="margin-right: 6px; color: #4f46e5; opacity: 0.7;"></i>
                                        {{ $file->created_at->format('d M Y') }}
                                        <small style="display: block; margin-top: 2px; opacity: 0.6;">{{ $file->created_at->format('H:i') }} WIB</small>
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="um-actions dk-actions-compact">
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                            class="dk-action-btn view"
                                            title="Pratinjau">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ asset('storage/' . $file->file_path) }}"
                                            download="{{ $file->original_name }}"
                                            class="dk-action-btn edit"
                                            style="--bg: rgba(16, 185, 129, 0.1); --color: #10b981;"
                                            title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <form action="{{ route('unit.form.destroy', $file->id) }}" method="POST" style="display: inline-flex;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="dk-action-btn delete"
                                                title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="padding: 60px 24px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, rgba(79,70,229,0.08), rgba(124,58,237,0.05)); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-inbox" style="font-size: 30px; color: var(--text-sub); opacity: 0.5;"></i>
                    </div>
                    <p style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--text-sub);">Belum ada laporan</p>
                    <p style="margin: 0; font-size: 12px; color: var(--text-sub); opacity: 0.7;">Upload laporan pertama Anda menggunakan form di atas</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>

<script>
    function laporanUploader() {
        return {
            dragover: false,
            fileName: '',
            fileSize: '',
            fileType: '', // pdf, word
            submitting: false,

            handleFile(event) {
                const file = event.target.files[0];
                if (file) this.processFile(file);
            },

            handleDrop(event) {
                this.dragover = false;
                const file = event.dataTransfer.files[0];
                if (file) {
                    // Set the file to the hidden input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;
                    this.processFile(file);
                }
            },

            processFile(file) {
                const allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];

                // Validate Type
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Valid',
                        text: 'Hanya file PDF atau Word (.doc, .docx) yang diperbolehkan.',
                        confirmButtonColor: '#4f46e5'
                    });
                    this.removeFile();
                    return;
                }

                // Validate size (3MB)
                if (file.size > 3 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Ukuran file maksimum adalah 3 MB.',
                        confirmButtonColor: '#4f46e5'
                    });
                    this.removeFile();
                    return;
                }

                this.fileName = file.name;
                this.fileSize = this.formatSize(file.size);
                this.fileType = file.type.includes('pdf') ? 'pdf' : 'word';
            },

            removeFile() {
                this.fileName = '';
                this.fileSize = '';
                this.fileType = '';
                this.$refs.fileInput.value = '';
            },

            formatSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };
    }
</script>