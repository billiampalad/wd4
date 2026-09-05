<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanKerjasama extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_kerjasamas';

    protected $fillable = [
        'cooperation_id',
        'nama_kegiatan',
        'periode_mulai',
        'periode_selesai',
        'status',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'tanggal_mou' => 'date',
    ];

    protected $appends = ['status_label', 'status_class'];

    // ─── Relationships ───────────────────────────────────

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }

    public function detailKegiatan()
    {
        return $this->hasOne(DetailKegiatan::class, 'kegiatan_kerjasama_id');
    }

    public function detailKegiatans()
    {
        return $this->hasMany(DetailKegiatan::class, 'kegiatan_kerjasama_id');
    }

    public function jenisKerjasama()
    {
        return $this->hasOneThrough(
            JenisKerjasama::class,
            DetailKegiatan::class,
            'kegiatan_kerjasama_id',
            'id',
            'id',
            'jenis_kerjasama_id'
        );
    }

    public function kegiatanMahasiswas()
    {
        return $this->hasMany(KegiatanMahasiswa::class, 'kegiatan_kerjasama_id');
    }

    public function mahasiswas()
    {
        return $this->belongsToMany(Mahasiswa::class, 'kegiatan_mahasiswas', 'kegiatan_kerjasama_id', 'mahasiswa_id');
    }

    public function evaluasis()
    {
        return $this->hasMany(Evaluasi::class, 'cooperation_id', 'cooperation_id');
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
