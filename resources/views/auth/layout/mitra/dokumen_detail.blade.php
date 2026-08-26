<main id="mainContent" class="dk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('mitra.dokumen.index') }}">Daftar Dokumen</a>
                <span>/</span>
                <span>Detail Dokumen</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-file-contract"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Detail Dokumen Kerjasama</h2>
                    <p class="ud-subtitle">{{ $cooperation->judul }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-md-8">
            <div class="dk-tabs-container mb-4">
                <h4 class="mb-3 border-bottom pb-2">Informasi Dokumen</h4>
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Judul</th>
                        <td>{{ $cooperation->judul }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Dokumen</th>
                        <td><span class="badge bg-primary">{{ $cooperation->jenis }}</span></td>
                    </tr>
                    <tr>
                        <th>Nomor Dokumen</th>
                        <td>{{ $cooperation->doc_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Dokumen</th>
                        <td>
                            @if(strtolower($cooperation->status_dokumen) == 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @else
                                <span class="badge bg-success">{{ $cooperation->status_dokumen }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status Berlaku</th>
                        <td>{{ ucfirst($cooperation->status_berlaku) }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            @if($cooperation->start_date && $cooperation->end_date)
                                {{ \Carbon\Carbon::parse($cooperation->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($cooperation->end_date)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if($cooperation->document_link)
                <div class="dk-tabs-container mb-4">
                    <h4 class="mb-3 border-bottom pb-2">File Dokumen</h4>
                    <a href="{{ $cooperation->document_link }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Buka Dokumen Online
                    </a>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="dk-tabs-container mb-4">
                <h4 class="mb-3 border-bottom pb-2">Kirim Review Draf</h4>
                <p class="text-muted small">Jika dokumen ini masih dalam tahap draf, Anda dapat memberikan catatan review kepada unit pengusul.</p>
                
                <form action="{{ route('mitra.dokumen.review', $cooperation->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="catatan_review" class="form-label">Catatan Review</label>
                        <textarea class="form-control" id="catatan_review" name="catatan_review" rows="5" required placeholder="Tuliskan masukan atau revisi yang diperlukan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="fas fa-paper-plane"></i> Kirim Catatan Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
