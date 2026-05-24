@if($kegiatan->status === 'revisi')
<div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--border); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
    <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 200px;">
        <div style="font-weight: 800; font-size: 13px; color: var(--text); display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-paper-plane" style="color: var(--accent);"></i>
                Kirim ulang ke Pimpinan
            </span>
            <span class="tag tag-orange" style="font-size: 11px; padding: 4px 10px;">
                <i class="fas fa-rotate" style="font-size: 10px;"></i> Sudah direvisi
            </span>
        </div>
        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.55;">
            Pastikan perbaikan sesuai catatan Pimpinan, lalu kirim kembali untuk evaluasi.
        </div>
    </div>
    <button type="button" onclick="confirmSubmitKerjasamaUnit()" style="padding: 10px 22px; background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(79,70,229,.28); white-space: nowrap;">
        <i class="fas fa-paper-plane"></i> Kirim ke Pimpinan
    </button>
</div>
@endif
