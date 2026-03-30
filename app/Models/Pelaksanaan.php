<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaans';

    protected $fillable = ['id_kegiatan', 'deskripsi', 'cakupan', 'jumlah_peserta', 'sumber_daya'];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
