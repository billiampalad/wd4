<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    use HasFactory;

    protected $table = 'dokumentasis';

    protected $fillable = ['id_kegiatan', 'link_drive', 'keterangan'];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
