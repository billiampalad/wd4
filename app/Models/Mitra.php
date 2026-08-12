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
        'klasifikasi_id',
        'alamat',
        'kota',
        'negara',
        'country_code',
        'provinsi',
        'province_code',
        'telepon',
        'website',
        'status_akses',
    ];

    /**
     * Virtual "kategori" — nasional jika Indonesia, internasional jika luar negeri.
     * ponytail: derived from country_code; add real column if classification grows beyond 2 values.
     */
    public function getKategoriAttribute(): string
    {
        if ($this->country_code === 'ID' || strtolower($this->negara ?? '') === 'indonesia') {
            return 'nasional';
        }

        return 'internasional';
    }

    public function scopeNasional($query)
    {
        return $query->where(function ($q) {
            $q->where('country_code', 'ID')
              ->orWhere('negara', 'like', '%indonesia%');
        });
    }

    public function scopeInternasional($query)
    {
        return $query->where(function ($q) {
            $q->where('country_code', '!=', 'ID')
              ->where(function ($q2) {
                  $q2->whereNull('negara')
                     ->orWhere('negara', 'not like', '%indonesia%');
              });
        })->whereNotNull('country_code');
    }

    public function scopeKategori($query, string $kategori)
    {
        return strtolower($kategori) === 'nasional'
            ? $query->nasional()
            : $query->internasional();
    }

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
        return $this->belongsTo(Klasifikasi::class, 'klasifikasi_id');
    }
}
