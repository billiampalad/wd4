<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    use HasFactory;

    protected $table = 'hasils';

    protected $fillable = [
        'id_kegiatan',
        'hasil_langsung',
        'dampak',
        'manfaat_mahasiswa',
        'manfaat_polimdo',
        'manfaat_mitra',
    ];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
