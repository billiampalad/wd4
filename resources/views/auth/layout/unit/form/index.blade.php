<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Kerjasama</span>
            <span class="sep">/</span>
            <span class="current">Form Laporan</span>
        </div>
        <h2 id="pageTitle">Form Laporan</h2>
        <p id="pageDesc">Upload dokumen laporan kerjasama dalam format PDF.</p>
    </div>

    <div style="width: 100%; max-width: 860px; margin: 0 auto;">

        {{-- ═══ Upload Card ═══ --}}
        <div class="modern-card" style="overflow: visible;">
            {{-- Card Header --}}
            <div style="display: flex; align-items: center; gap: 16px; padding: 24px 28px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.04), rgba(124,58,237,0.03)); border-radius: 16px 16px 0 0;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text); letter-spacing: -0.02em;">Upload Laporan PDF</h3>
                    <p style="margin: 3px 0 0; font-size: 12px; color: var(--text-sub);">Unggah dokumen laporan kerjasama untuk dokumentasi dan arsip</p>
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding: 28px;" x-data="pdfUploader()">
                <form action="{{ route('unit.form.store') }}" method="POST" enctype="multipart/form-data" @submit="submitting = true">
                    @csrf


                    {{-- ─── Drag & Drop Zone ─── --}}
                    <div style="margin-bottom: 28px;">
                        <label class="mc-label" style="margin-bottom: 10px;">File PDF <span class="mc-req">*</span></label>

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
                            style="position: relative; border: 2px dashed var(--border); border-radius: 16px; padding: 40px 24px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: var(--surface2);"
                        >
                            <input type="file" name="file_pdf" accept=".pdf" x-ref="fileInput" @change="handleFile($event)" style="display: none;" required />

                            {{-- Empty State --}}
                            <div x-show="!fileName" style="pointer-events: none;">
                                <div style="width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.08)); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-cloud-arrow-up" style="font-size: 28px; color: #4f46e5;"></i>
                                </div>
                                <p style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--text);">
                                    Drag & drop file PDF di sini
                                </p>
                                <p style="margin: 0 0 14px; font-size: 12px; color: var(--text-sub);">
                                    atau klik untuk memilih file dari perangkat Anda
                                </p>
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; font-size: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(79,70,229,0.3); pointer-events: none;">
                                    <i class="fas fa-folder-open"></i>
                                    Pilih File
                                </div>
                                <p style="margin: 12px 0 0; font-size: 11px; color: var(--text-sub);">
                                    <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                    Maksimum 10 MB — hanya file .pdf
                                </p>
                            </div>

                            {{-- File Selected State --}}
                            <div x-show="fileName" x-cloak style="pointer-events: none;">
                                <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
                                    {{-- PDF Icon --}}
                                    <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 4px 14px rgba(239,68,68,0.3);">
                                        <i class="fas fa-file-pdf"></i>
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
                                onmouseout="this.style.background='rgba(239,68,68,0.08)'"
                            >
                                <i class="fas fa-trash-alt"></i> Hapus File
                            </button>
                        </div>

                        @error('file_pdf')
                            <span style="color: #ef4444; font-size: 11px; margin-top: 8px; display: block;">
                                <i class="fas fa-circle-exclamation"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- ─── Submit Buttons ─── --}}
                    <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border);">
                        <a href="{{ route('unit.form') }}" class="rfc-btn"
                            style="text-decoration: none; background: var(--surface); color: var(--text); border: 1px solid var(--border); padding: 12px 24px; border-radius: 12px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;"
                            onmouseover="this.style.background='var(--surface2)'"
                            onmouseout="this.style.background='var(--surface)'"
                        >
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" :disabled="submitting || !fileName"
                            style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; border: none; padding: 12px 28px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(79,70,229,0.35); transition: all 0.3s;"
                            :style="(!fileName || submitting) ? 'opacity: 0.5; cursor: not-allowed;' : ''"
                            onmouseover="if(!this.disabled) { this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(79,70,229,0.45)'; }"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(79,70,229,0.35)';"
                        >
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
        <div class="modern-card" style="margin-top: 24px; overflow: visible;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 28px; border-bottom: 1px solid var(--border);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; box-shadow: 0 3px 10px rgba(5,150,105,0.3);">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 14px; font-weight: 700; color: var(--text);">Riwayat Laporan</h4>
                        <p style="margin: 2px 0 0; font-size: 11px; color: var(--text-sub);">Dokumen yang telah diunggah sebelumnya</p>
                    </div>
                </div>
                <span style="background: rgba(79,70,229,0.1); color: #4f46e5; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
                    {{ isset($laporanFiles) ? $laporanFiles->count() : 0 }} File
                </span>
            </div>

            <div style="padding: 0;">
                @if(isset($laporanFiles) && $laporanFiles->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: var(--surface2);">
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em;">No</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em;">Nama File</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em;">Tanggal Upload</th>
                                    <th style="padding: 12px 20px; text-align: center; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laporanFiles as $index => $file)
                                    <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;"
                                        onmouseover="this.style.background='var(--surface2)'"
                                        onmouseout="this.style.background='transparent'">
                                        <td style="padding: 14px 20px;">
                                            <span style="width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.08)); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td style="padding: 14px 20px;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div>
                                                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: var(--text); max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $file->original_name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 14px 20px;">
                                            <span style="font-size: 12px; color: var(--text-sub);">
                                                <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                                                {{ $file->created_at->format('d M Y, H:i') }}
                                            </span>
                                        </td>
                                        <td style="padding: 14px 20px; text-align: center;">
                                            <div style="display: inline-flex; gap: 6px;">
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                    style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; text-decoration: none; transition: all 0.2s;"
                                                    onmouseover="this.style.background='rgba(79,70,229,0.2)'"
                                                    onmouseout="this.style.background='rgba(79,70,229,0.1)'"
                                                    title="Lihat PDF">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" download
                                                    style="width: 32px; height: 32px; border-radius: 8px; background: rgba(5,150,105,0.1); color: #059669; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; text-decoration: none; transition: all 0.2s;"
                                                    onmouseover="this.style.background='rgba(5,150,105,0.2)'"
                                                    onmouseout="this.style.background='rgba(5,150,105,0.1)'"
                                                    title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('unit.form.destroy', $file->id) }}" method="POST" style="display: inline-flex;"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239,68,68,0.1); color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; border: none; cursor: pointer; transition: all 0.2s;"
                                                        onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                                                        onmouseout="this.style.background='rgba(239,68,68,0.1)'"
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
function pdfUploader() {
    return {
        dragover: false,
        fileName: '',
        fileSize: '',
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
            // Validate PDF
            if (file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid',
                    text: 'Hanya file PDF yang diperbolehkan.',
                    confirmButtonColor: '#4f46e5'
                });
                this.removeFile();
                return;
            }

            // Validate size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Terlalu Besar',
                    text: 'Ukuran file maksimum adalah 10 MB.',
                    confirmButtonColor: '#4f46e5'
                });
                this.removeFile();
                return;
            }

            this.fileName = file.name;
            this.fileSize = this.formatSize(file.size);
        },

        removeFile() {
            this.fileName = '';
            this.fileSize = '';
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
