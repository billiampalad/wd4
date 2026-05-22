<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooperation extends Model
{
    use HasFactory;

    protected $table = 'cooperations';

    protected $fillable = [
        'jenis',
        'doc_number',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'document_link',
        'mitra_id',
        'internal_instansi',
        'penandatangan_internal_id',
        'pj_internal_id',
        'penandatangan_mitra_id',
        'pj_mitra_id',
        'tipe_pelaksana',
        'jurusan_id',
        'upa_id',
        'pusat_id',
        'status_dokumen',
        'perpanjangan_dari_id',
    ];

    public const DEFAULT_MOU_PELAKSANA = 'Politeknik Negeri Manado';

    protected $appends = ['status_label', 'status_class'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function penandatanganInternal()
    {
        return $this->belongsTo(Pejabat::class, 'penandatangan_internal_id');
    }

    public function pjInternal()
    {
        return $this->belongsTo(Pejabat::class, 'pj_internal_id');
    }

    public function penandatanganMitra()
    {
        return $this->belongsTo(Pejabat::class, 'penandatangan_mitra_id');
    }

    public function pjMitra()
    {
        return $this->belongsTo(Pejabat::class, 'pj_mitra_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function upa()
    {
        return $this->belongsTo(Upa::class, 'upa_id');
    }

    public function pusat()
    {
        return $this->belongsTo(Pusat::class, 'pusat_id');
    }

    public function jurusans()
    {
        return $this->belongsToMany(Jurusan::class, 'kerjasama_jurusan', 'cooperation_id', 'jurusan_id');
    }

    public function upas()
    {
        return $this->belongsToMany(Upa::class, 'kerjasama_upa', 'cooperation_id', 'upa_id');
    }

    public function pusats()
    {
        return $this->belongsToMany(Pusat::class, 'kerjasama_pusat', 'cooperation_id', 'pusat_id');
    }

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'kerjasama_prodi', 'cooperation_id', 'prodi_id');
    }

    public function laporanFiles()
    {
        return $this->hasMany(LaporanFile::class, 'cooperation_id');
    }

    public function pksNumbers()
    {
        return $this->hasMany(PksNumber::class, 'cooperation_id')->orderBy('sort_order')->orderBy('id');
    }

    public function details()
    {
        return $this->hasMany(DetailKegiatan::class);
    }

    public function evaluasis()
    {
        return $this->hasMany(Evaluasi::class, 'cooperation_id');
    }

    public function perpanjanganDari()
    {
        return $this->belongsTo(self::class, 'perpanjangan_dari_id');
    }

    public function perpanjangans()
    {
        return $this->hasMany(self::class, 'perpanjangan_dari_id');
    }

    public function kesimpulans()
    {
        return $this->hasMany(Evaluasi::class, 'cooperation_id');
    }

    public function getPksNumberAttribute($value)
    {
        if ($this->relationLoaded('pksNumbers')) {
            return $this->pksNumbers->pluck('number')->filter()->implode(', ');
        }

        return $value ?: $this->pksNumbers()->pluck('number')->filter()->implode(', ');
    }

    // ─── Accessors ───────────────────────────────────────

    public function getStatusLabelAttribute()
    {
        return $this->status_dokumen ?: 'Draft';
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status_dokumen) {
            'Menunggu Evaluasi' => 'tag-blue',
            'Menunggu Validasi' => 'tag-purple',
            'Disahkan' => 'tag-green',
            'Revisi' => 'tag-red',
            default => 'tag-orange',
        };
    }

    public function getPelaksanaNameAttribute()
    {
        return match ($this->tipe_pelaksana) {
            'jurusan' => $this->jurusan?->nama_jurusan ?: '-',
            'upa' => $this->upa?->nama_upa ?: '-',
            'pusat' => $this->pusat?->nama_pusat ?: '-',
            default => str_contains(strtolower($this->jenis ?? ''), 'mou')
                ? self::DEFAULT_MOU_PELAKSANA
                : '-',
        };
    }

    public function getPelaksanaIconAttribute()
    {
        return match ($this->tipe_pelaksana) {
            'jurusan' => 'fa-microchip',
            'upa' => 'fa-building-columns',
            'pusat' => 'fa-landmark',
            default => 'fa-building',
        };
    }

    public function getPelaksanaClassAttribute()
    {
        return match ($this->tipe_pelaksana) {
            'upa' => 'dk-entity-cyan',
            'pusat' => 'dk-entity-violet',
            default => 'dk-entity-indigo',
        };
    }
}
