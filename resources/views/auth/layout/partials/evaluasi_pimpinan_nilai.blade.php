@php
    $nilaiEvaluasi = [
        ['label' => 'Sesuai Rencana', 'value' => $evaluasi->sesuai_rencana ?? null, 'color' => '#2563eb'],
        ['label' => 'Kualitas Pelaksanaan', 'value' => $evaluasi->kualitas ?? null, 'color' => '#7c3aed'],
        ['label' => 'Keterlibatan Mitra', 'value' => $evaluasi->keterlibatan ?? null, 'color' => '#0891b2'],
        ['label' => 'Efisiensi Anggaran', 'value' => $evaluasi->efisiensi ?? null, 'color' => '#059669'],
        ['label' => 'Tingkat Kepuasan', 'value' => $evaluasi->kepuasan ?? null, 'color' => '#d97706'],
    ];

    $nilaiTersedia = collect($nilaiEvaluasi)->filter(fn ($item) => $item['value'] !== null)->values();
    $rataRataEvaluasi = $nilaiTersedia->isNotEmpty()
        ? round($nilaiTersedia->avg('value'), 1)
        : null;
@endphp

@if ($nilaiTersedia->isNotEmpty())
    <div style="margin-bottom: 20px;">
        <label
            style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
            <i class="fas fa-star" style="margin-right: 4px; opacity: 0.5;"></i> Nilai Evaluasi Pimpinan
        </label>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
            <div
                style="padding: 16px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(37, 99, 235, 0.08)); border: 1px solid var(--border); border-radius: 12px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">
                    Rata-rata
                </div>
                <div style="display: flex; align-items: baseline; gap: 4px;">
                    <strong style="font-size: 28px; line-height: 1; color: #059669;">{{ $rataRataEvaluasi }}</strong>
                    <span style="font-size: 13px; font-weight: 700; color: var(--text-sub);">/5</span>
                </div>
            </div>

            @foreach ($nilaiEvaluasi as $item)
                @if ($item['value'] !== null)
                    <div style="padding: 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-sub); margin-bottom: 8px; min-height: 28px;">
                            {{ $item['label'] }}
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div>
                                <strong style="font-size: 22px; line-height: 1; color: {{ $item['color'] }};">{{ $item['value'] }}</strong>
                                <span style="font-size: 12px; font-weight: 700; color: var(--text-sub);">/5</span>
                            </div>
                            <div style="display: flex; gap: 2px; color: #f59e0b; font-size: 11px; flex-shrink: 0;">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= (int) $item['value'] ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif
