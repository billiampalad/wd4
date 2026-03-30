<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mitra',
        'negara',
        'kategori',
    ];

    public function kegiatanMitras()
    {
        return $this->hasMany(KegiatanMitra::class, 'id_mitra');
    }

    public function kegiatanKerjasamas()
    {
        return $this->belongsToMany(KegiatanKerjasama::class, 'kegiatan_mitras', 'id_mitra', 'id_kegiatan');
    }
}
