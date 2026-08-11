<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_mahasiswas';

    protected $fillable = [
        'kegiatan_id',
        'mahasiswa_id',
        'mitra_id',
        'periode_mulai',
        'periode_selesai',
        'status',
        'nilai_mitra',
        'catatan_mitra',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'nilai_mitra' => 'decimal:2',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'kegiatan_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function pembimbings()
    {
        return $this->hasMany(Pembimbing::class);
    }
}
