<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKerjasamaMitra extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    protected $table = 'pengajuan_kerjasama_mitras';

    protected $fillable = [
        'kode_pengajuan',
        'nama_mitra',
        'id_klasifikasi',
        'kategori',
        'negara',
        'alamat',
        'telp',
        'website',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'nama_penanggung_jawab',
        'jabatan_penanggung_jawab',
        'email',
        'telepon',
        'judul_pengajuan',
        'tujuan_pengajuan',
        'ruang_lingkup',
        'pesan_tambahan',
        'status',
        'catatan_pimpinan',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'mitra_id',
        'jenis',
        'doc_number',
        'start_date',
        'end_date',
    ];

    protected $appends = [
        'status_label',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function klasifikasi(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class, 'id_klasifikasi');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Diajukan',
        };
    }
}
