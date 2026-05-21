<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    //
    protected $fillable = [
        'cooperation_id',
        'jenis_kerjasama_id',
        'sasaran_id',
        'nilai_kontrak',
        'income',
        'volume_luaran',
        'satuan_luaran',
        'keterangan',
        'tujuan',
        'indikator_kinerja',
        'output',
        'outcome',
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function jenisKerjasama()
    {
        return $this->belongsTo(JenisKerjasama::class, 'jenis_kerjasama_id');
    }

    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }
}
