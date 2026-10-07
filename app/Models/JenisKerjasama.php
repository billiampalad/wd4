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
        return $this->hasManyThrough(
            KegiatanKerjasama::class,
            DetailKegiatan::class,
            'jenis_kerjasama_id',
            'id',
            'id',
            'kegiatan_kerjasama_id'
        );
    }

    public function details()
    {
        return $this->hasMany(DetailKegiatan::class, 'jenis_kerjasama_id');
    }

    public function getNamaJenisAttribute()
    {
        return $this->attributes['nama_kerjasama'] ?? ($this->attributes['nama'] ?? null);
    }

    public function getNamaKerjasamaAttribute()
    {
        return $this->attributes['nama_kerjasama'] ?? ($this->attributes['nama'] ?? null);
    }

    public function getNamaAttribute()
    {
        return $this->attributes['nama_kerjasama'] ?? ($this->attributes['nama'] ?? null);
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama_kerjasama'] = $value;
    }
}

