{{-- Quick View Modal / Detail Ringkasan Evaluasi --}}
<div x-show="showModal" 
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;">
    
    <div @click.outside="showModal = false"
        x-show="showModal"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
        style="background: var(--surface, #ffffff); border: 1px solid var(--border, rgba(226, 232, 240, 0.8)); border-radius: 20px; width: 100%; max-width: 580px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; position: relative;">
        
        {{-- Modal Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--border, rgba(226, 232, 240, 0.8)); background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(124,58,237,0.04));">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text, #0f172a);">Ringkasan Evaluasi Kerja Sama</h3>
                    <small style="color: var(--text-sub, #64748b); font-size: 11px;">Data capaian dan penilaian pelaksanaan kemitraan</small>
                </div>
            </div>
            <button type="button" @click="showModal = false"
                style="background: rgba(0,0,0,0.05); border: none; font-size: 15px; color: var(--text-sub, #64748b); cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div style="padding: 24px; overflow-y: auto;">
            {{-- Document Summary Box --}}
            <div style="background: var(--surface2, #f8fafc); padding: 16px 18px; border-radius: 14px; border: 1px solid var(--border, rgba(226, 232, 240, 0.8)); margin-bottom: 18px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-file-contract" style="color: #4f46e5; font-size: 12px;"></i>
                    <span style="font-size: 12px; font-family: monospace; color: #4f46e5; font-weight: 700;" x-text="selectedItem.nomor"></span>
                </div>
                <h4 style="margin: 6px 0 0 0; font-size: 15px; font-weight: 800; color: var(--text, #0f172a); line-height: 1.4;" x-text="selectedItem.judul"></h4>
                <div style="font-size: 12px; color: var(--text-sub, #64748b); margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
                    <span><strong>Mitra:</strong> <span x-text="selectedItem.mitra"></span></span>
                    <span>•</span>
                    <span><strong>Periode:</strong> <span x-text="selectedItem.periode"></span></span>
                </div>
            </div>

            {{-- Metric Cards --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                <div style="background: var(--surface, #ffffff); padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border, rgba(226, 232, 240, 0.8)); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <small style="font-size: 11px; color: var(--text-sub, #64748b); display: block; margin-bottom: 4px;">Skor Capaian Mitra</small>
                    <strong style="font-size: 15px; color: #059669; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-star" style="color: #d97706; font-size: 13px;"></i>
                        <span x-text="selectedItem.skor"></span>
                    </strong>
                </div>
                <div style="background: var(--surface, #ffffff); padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border, rgba(226, 232, 240, 0.8)); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <small style="font-size: 11px; color: var(--text-sub, #64748b); display: block; margin-bottom: 4px;">Status Pelaksanaan</small>
                    <strong style="font-size: 14px; color: #4f46e5; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-circle-check" style="font-size: 13px;"></i>
                        <span x-text="selectedItem.status"></span>
                    </strong>
                </div>
            </div>

            {{-- Text Descriptions --}}
            <div style="border-top: 1px solid var(--border, rgba(226, 232, 240, 0.8)); padding-top: 16px;">
                <h5 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 800; color: var(--text, #0f172a); display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-file-lines" style="color: #4f46e5;"></i> Ringkasan Realisasi &amp; Luaran:
                </h5>
                <p style="margin: 0 0 16px 0; font-size: 13px; color: var(--text, #334155); line-height: 1.6;" x-text="selectedItem.evaluasiText"></p>

                <h5 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 800; color: var(--text, #0f172a); display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-lightbulb" style="color: #d97706;"></i> Rekomendasi Tindak Lanjut:
                </h5>
                <p style="margin: 0; font-size: 13px; color: var(--text-sub, #64748b); font-style: italic; line-height: 1.6;" x-text="selectedItem.catatan"></p>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="display: flex; justify-content: flex-end; padding: 14px 24px; border-top: 1px solid var(--border, rgba(226, 232, 240, 0.8)); background: var(--surface2, #f8fafc);">
            <button type="button" class="rfc-btn" @click="showModal = false"
                style="padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; background: var(--surface, #ffffff); color: var(--text, #0f172a); border: 1px solid var(--border, #cbd5e1); cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                Tutup
            </button>
        </div>

    </div>
</div>
