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
        'telp',
        'website',
    ];

    public function cooperations()
    {
        return $this->hasMany(Cooperation::class, 'mitra_id');
    }

    public function klasifikasi(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class, 'id_klasifikasi');
    }
}
