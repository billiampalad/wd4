<div class="content-header">
    <div>
        <h2>Dokumen Kerja Sama</h2>
        <p>Kelola dan tinjau seluruh dokumen kerja sama (MoU, MoA, IA) institusi Anda.</p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="justify-content: space-between; align-items: center;">
        <h3>Daftar Dokumen</h3>
        
        <form action="{{ route('mitra.dokumen.index') }}" method="GET" class="filter-form" style="display: flex; gap: 10px;">
            <select name="jenis" class="form-input" style="width: auto; padding: 6px 12px; border-radius: 6px;">
                <option value="">-- Semua Jenis --</option>
                <option value="MoU" {{ request('jenis') == 'MoU' ? 'selected' : '' }}>MoU</option>
                <option value="MoA" {{ request('jenis') == 'MoA' ? 'selected' : '' }}>MoA</option>
                <option value="IA" {{ request('jenis') == 'IA' ? 'selected' : '' }}>IA</option>
            </select>
            <select name="status" class="form-input" style="width: auto; padding: 6px 12px; border-radius: 6px;">
                <option value="">-- Semua Status --</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Kadaluarsa" {{ request('status') == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 6px 12px;"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Judul Dokumen</th>
                    <th width="15%">Jenis & Status</th>
                    <th width="20%">Periode</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cooperations as $index => $coop)
                    <tr>
                        <td style="text-align: center;">{{ $cooperations->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $coop->judul ?: 'Tanpa Judul' }}</strong>
                            <div style="font-size: 0.85em; color: #64748b; margin-top: 4px;">
                                No: {{ $coop->doc_number ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background-color: var(--primary-color); color: white; margin-bottom:4px;">{{ $coop->jenis }}</span><br>
                            @if($coop->status_dokumen == 'Draft' || str_contains($coop->status_dokumen, 'Menunggu'))
                                <span class="badge" style="background-color: #f59e0b; color: white;">Draft / Menunggu</span>
                            @else
                                <span class="badge" style="background-color: {{ $coop->status_berlaku == 'Aktif' ? '#10b981' : ($coop->status_berlaku == 'Kadaluarsa' ? '#ef4444' : '#64748b') }}; color: white;">
                                    {{ $coop->status_berlaku ?: 'Menunggu' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.9em;">
                                <i class="far fa-calendar-alt" style="color: #64748b; width:16px;"></i> 
                                {{ $coop->start_date ? \Carbon\Carbon::parse($coop->start_date)->format('d M Y') : '-' }}
                                <br>
                                <i class="far fa-calendar-check" style="color: #64748b; width:16px;"></i> 
                                {{ $coop->end_date ? \Carbon\Carbon::parse($coop->end_date)->format('d M Y') : '-' }}
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('mitra.dokumen.show', $coop->id) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85em;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 30px;">
                            <div style="color: #94a3b8; margin-bottom: 10px;">
                                <i class="fas fa-folder-open fa-3x"></i>
                            </div>
                            Belum ada dokumen kerja sama yang terdaftar untuk institusi Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cooperations->hasPages())
    <div style="margin-top: 20px; padding: 0 20px 20px 20px;">
        {{ $cooperations->withQueryString()->links() }}
    </div>
    @endif
</div>
