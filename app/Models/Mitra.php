<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mitra',
        'id_klasifikasi',
        'alamat',
        'kategori',
        'negara',
        'country_code',
        'provinsi',
        'province_code',
        'telp',
        'email',
        'website',
    ];

    public function cooperations()
    {
        return $this->hasMany(Cooperation::class, 'mitra_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function kegiatanMahasiswas()
    {
        return $this->hasMany(KegiatanMahasiswa::class);
    }

    public function alumniMitras()
    {
        return $this->hasMany(AlumniMitra::class);
    }

    public function klasifikasi(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class, 'id_klasifikasi');
    }
}
