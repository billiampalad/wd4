<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mitra',
        'id_klasifikasi',
        'alamat',
        'kategori',
        'negara',
        'telp',
        'website',
    ];

    public function kegiatanMitras()
    {
        return $this->hasMany(KegiatanMitra::class, 'id_mitra');
    }

    public function kegiatanKerjasamas()
    {
        return $this->belongsToMany(KegiatanKerjasama::class, 'kegiatan_mitras', 'id_mitra', 'id_kegiatan');
    }

    public function klasifikasi(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class, 'id_klasifikasi');
    }
}
