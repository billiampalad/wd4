<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasis';

    protected $fillable = [
        'id_kegiatan',
        'dinilai_oleh',
        'sesuai_rencana',
        'kualitas',
        'keterlibatan',
        'efisiensi',
        'kepuasan',
        'catatan',
    ];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}
