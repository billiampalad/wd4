<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembimbing extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_mahasiswa_id',
        'nama_pembimbing',
        'tipe',
        'kontak',
    ];

    public function kegiatanMahasiswa()
    {
        return $this->belongsTo(KegiatanMahasiswa::class);
    }
}
