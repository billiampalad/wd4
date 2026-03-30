<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKerjasama extends Model
{
    use HasFactory;

    protected $table = 'jenis_kerjasamas';

    protected $fillable = ['nama_kerjasama'];

    public function kegiatanKerjasamas()
    {
        return $this->belongsToMany(KegiatanKerjasama::class, 'kegiatan_jenis_kerjasamas', 'id_jenis', 'id_kegiatan');
    }
}
