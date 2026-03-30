<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanMitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kegiatan',
        'id_mitra',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
