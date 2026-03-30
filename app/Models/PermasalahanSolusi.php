<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermasalahanSolusi extends Model
{
    use HasFactory;

    protected $table = 'permasalahan_solusis';

    protected $fillable = [
        'id_kegiatan',
        'kendala',
        'solusi',
        'rekomendasi',
    ];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
