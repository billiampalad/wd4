<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanKerjasama extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_kerjasamas';

    protected $fillable = [
        'nama_kegiatan',
        'created_by',
        'periode_mulai',
        'periode_selesai',
        'nomor_mou',
        'tanggal_mou',
        'penanggung_jawab',
        'status',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'tanggal_mou' => 'date',
    ];

    protected $appends = ['status_label', 'status_class'];

    // ─── Relationships ───────────────────────────────────

    public function jenisKerjasama()
    {
        return $this->belongsToMany(JenisKerjasama::class, 'kegiatan_jenis_kerjasamas', 'id_kegiatan', 'id_jenis');
    }

    public function jurusans()
    {
        return $this->belongsToMany(Jurusan::class, 'kegiatan_jurusans', 'id_kegiatan', 'id_jurusan');
    }

    public function unitKerjas()
    {
        return $this->belongsToMany(UnitKerja::class, 'kegiatan_units', 'id_kegiatan', 'id_unit');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mitras()
    {
        return $this->belongsToMany(Mitra::class, 'kegiatan_mitras', 'id_kegiatan', 'id_mitra');
    }

    public function tujuans()
    {
        return $this->hasMany(Tujuan::class, 'id_kegiatan');
    }

    public function pelaksanaans()
    {
        return $this->hasMany(Pelaksanaan::class, 'id_kegiatan');
    }

    public function hasils()
    {
        return $this->hasMany(Hasil::class, 'id_kegiatan');
    }

    public function dokumentasis()
    {
        return $this->hasMany(Dokumentasi::class, 'id_kegiatan');
    }

    public function evaluasis()
    {
        return $this->hasMany(Evaluasi::class, 'id_kegiatan');
    }

    public function kesimpulans()
    {
        return $this->hasMany(Kesimpulan::class, 'id_kegiatan');
    }

    public function permasalahanSolusis()
    {
        return $this->hasMany(PermasalahanSolusi::class, 'id_kegiatan');
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isAktif()
    {
        if (!$this->periode_selesai) return true;
        return now()->isBefore($this->periode_selesai);
    }

    /**
     * Display status label based on DB status column
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'menunggu_evaluasi' => 'Menunggu Evaluasi',
            'menunggu_validasi' => 'Menunggu Validasi Pimpinan',
            'selesai' => 'Selesai',
            'revisi' => 'Perlu Revisi',
            default => 'Draft',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            'menunggu_evaluasi' => 'tag-blue',
            'menunggu_validasi' => 'tag-purple',
            'selesai' => 'tag-green',
            'revisi' => 'tag-red',
            default => 'tag-orange',
        };
    }
}
