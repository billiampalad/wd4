<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    //
    protected $fillable = [
        'kegiatan_kerjasama_id',
        'cooperation_id',
        'jenis_kerjasama_id',
        'sasaran_id',
        'indikator_id',
        'income',
        'volume_luaran',
        'keterangan_luaran',
        'output',
        'outcome',
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'kegiatan_kerjasama_id');
    }

    public function jenisKerjasama()
    {
        return $this->belongsTo(JenisKerjasama::class, 'jenis_kerjasama_id');
    }

    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
