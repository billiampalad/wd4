<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'nama',
        'prodi_id',
        'angkatan',
        'email',
        'telepon',
        'status',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function kegiatanMahasiswas()
    {
        return $this->hasMany(KegiatanMahasiswa::class);
    }
}
