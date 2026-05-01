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
        'pks_number',
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
    ];
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

    public function details()
    {
        return $this->hasMany(DetailKegiatan::class);
    }
}
