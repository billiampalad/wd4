{{-- Quick View Modal / Detail Ringkasan Evaluasi --}}
<div x-show="showModal" style="display: none;"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
    
    <div @click.outside="showModal = false"
        style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
        
        {{-- Modal Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(124,58,237,0.04));">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text);">Ringkasan Evaluasi Kerja Sama</h3>
                    <small style="color: var(--text-sub); font-size: 11px;">Data capaian dan penilaian pelaksanaan kemitraan</small>
                </div>
            </div>
            <button type="button" @click="showModal = false"
                style="background: none; border: none; font-size: 16px; color: var(--text-sub); cursor: pointer; padding: 4px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="padding: 24px;">
            <div style="background: var(--surface2); padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 18px;">
                <span style="font-size: 11px; font-family: monospace; color: #4f46e5; font-weight: 700;" x-text="selectedItem.nomor"></span>
                <h4 style="margin: 4px 0 0 0; font-size: 14px; font-weight: 800; color: var(--text);" x-text="selectedItem.judul"></h4>
                <div style="font-size: 12px; color: var(--text-sub); margin-top: 6px;">
                    <strong>Mitra:</strong> <span x-text="selectedItem.mitra"></span> • <strong>Periode:</strong> <span x-text="selectedItem.periode"></span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                <div style="background: var(--surface); padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border);">
                    <small style="font-size: 11px; color: var(--text-sub); display: block;">Skor Capaian Mitra</small>
                    <strong style="font-size: 14px; color: #059669;" x-text="selectedItem.skor"></strong>
                </div>
                <div style="background: var(--surface); padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border);">
                    <small style="font-size: 11px; color: var(--text-sub); display: block;">Status Pelaksanaan</small>
                    <strong style="font-size: 14px; color: #4f46e5;" x-text="selectedItem.status"></strong>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 14px;">
                <h5 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 800; color: var(--text);">
                    <i class="fas fa-file-lines" style="color: #4f46e5; margin-right: 4px;"></i> Ringkasan Realisasi &amp; Luaran:
                </h5>
                <p style="margin: 0 0 12px 0; font-size: 12px; color: var(--text); line-height: 1.5;" x-text="selectedItem.evaluasiText"></p>

                <h5 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 800; color: var(--text);">
                    <i class="fas fa-lightbulb" style="color: #d97706; margin-right: 4px;"></i> Rekomendasi Tindak Lanjut:
                </h5>
                <p style="margin: 0; font-size: 12px; color: var(--text-sub); font-style: italic; line-height: 1.5;" x-text="selectedItem.catatan"></p>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="display: flex; justify-content: flex-end; padding: 14px 24px; border-top: 1px solid var(--border); background: var(--surface2);">
            <button type="button" class="rfc-btn" @click="showModal = false"
                style="padding: 8px 18px; border-radius: 8px; font-size: 12px; background: var(--surface); color: var(--text); border: 1px solid var(--border); cursor: pointer;">
                Tutup
            </button>
        </div>

    </div>
</div>
