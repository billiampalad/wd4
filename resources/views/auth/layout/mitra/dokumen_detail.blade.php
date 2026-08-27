<div class="content-header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h2>Detail Dokumen Kerja Sama</h2>
            <p>Lihat detail atau berikan review draf dokumen kerja sama.</p>
        </div>
        <a href="{{ route('mitra.dokumen.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="detail-container" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Bagian Kiri: Info Dokumen -->
    <div class="card" style="padding: 20px;">
        <h3 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
            <i class="fas fa-file-contract"></i> {{ $cooperation->judul ?: 'Tanpa Judul' }}
        </h3>
        
        <table class="table-detail" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; width: 30%; color: #64748b;">Nomor Dokumen</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-weight: 500;">{{ $cooperation->doc_number ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Jenis</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">{{ $cooperation->jenis }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Tingkat</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">{{ $cooperation->tingkat }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Status Berlaku</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                    @if($cooperation->status_berlaku == 'Aktif')
                        <span class="badge" style="background-color: #10b981; color: white;">Aktif</span>
                    @elseif($cooperation->status_berlaku == 'Kadaluarsa')
                        <span class="badge" style="background-color: #ef4444; color: white;">Kadaluarsa</span>
                    @else
                        <span class="badge" style="background-color: #64748b; color: white;">{{ $cooperation->status_berlaku ?: 'Draft' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Status Dokumen</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                    <span class="badge" style="background-color: {{ str_contains($cooperation->status_dokumen, 'Draft') ? '#f59e0b' : '#3b82f6' }}; color: white;">
                        {{ $cooperation->status_dokumen ?: 'Draft' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Periode</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                    {{ $cooperation->start_date ? \Carbon\Carbon::parse($cooperation->start_date)->format('d M Y') : '-' }} 
                    <i class="fas fa-arrow-right" style="margin: 0 10px; color: #94a3b8;"></i> 
                    {{ $cooperation->end_date ? \Carbon\Carbon::parse($cooperation->end_date)->format('d M Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b;">Ruang Lingkup</td>
                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">{{ $cooperation->deskripsi ?: '-' }}</td>
            </tr>
        </table>
        
        <h4 style="margin-bottom: 15px;"><i class="fas fa-paperclip"></i> Lampiran Dokumen</h4>
        @if($cooperation->document_link)
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-file-pdf fa-2x" style="color: #ef4444;"></i>
                    <div>
                        <div style="font-weight: 600;">Draf / Scan Dokumen PDF</div>
                        <div style="font-size: 0.85em; color: #64748b;">Klik tombol di samping untuk melihat atau mengunduh</div>
                    </div>
                </div>
                <a href="{{ $cooperation->document_link }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-download"></i> Unduh PDF
                </a>
            </div>
        @else
            <div style="background-color: #fffbeb; padding: 15px; border-radius: 8px; border: 1px dashed #f59e0b; color: #b45309;">
                <i class="fas fa-exclamation-triangle"></i> Dokumen lampiran (PDF) belum tersedia.
            </div>
        @endif
    </div>
    
    <!-- Bagian Kanan: Form Review -->
    <div class="card" style="padding: 20px;">
        <h3 style="margin-bottom: 15px;"><i class="fas fa-comment-dots"></i> Review Draf Dokumen</h3>
        
        @if(str_contains(strtolower($cooperation->status_dokumen), 'draft') || str_contains(strtolower($cooperation->status_dokumen), 'menunggu'))
            <p style="font-size: 0.9em; color: #64748b; margin-bottom: 20px; line-height: 1.5;">
                Silakan periksa draf dokumen yang dilampirkan. Jika ada penyesuaian terkait pasal, hak, kewajiban, atau ruang lingkup, mohon tuliskan catatan Anda di bawah ini agar Unit Pengusul dapat merevisinya.
            </p>
            
            <form action="{{ route('mitra.dokumen.review', $cooperation->id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Catatan Review <span style="color: red;">*</span></label>
                    <textarea name="catatan_review" class="form-input" rows="6" placeholder="Ketikkan poin-poin yang perlu diperbaiki (contoh: 'Pada Pasal 3 ayat 2, mohon perjelas nominal...')" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; justify-content: center;">
                    <i class="fas fa-paper-plane"></i> Kirim Catatan Review
                </button>
            </form>
        @else
            <div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; border: 1px solid #bbf7d0; text-align: center;">
                <i class="fas fa-check-circle fa-3x" style="color: #22c55e; margin-bottom: 15px;"></i>
                <div style="font-weight: 600; color: #166534; margin-bottom: 5px;">Dokumen Tidak Memerlukan Review</div>
                <div style="font-size: 0.9em; color: #15803d;">
                    Dokumen ini berstatus <strong>{{ $cooperation->status_dokumen }}</strong> dan sudah disahkan atau tidak berada pada tahap draf/review.
                </div>
            </div>
        @endif
    </div>
</div>
