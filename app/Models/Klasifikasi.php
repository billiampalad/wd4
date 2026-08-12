<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klasifikasi extends Model
{
    use HasFactory;

    protected $table = 'klasifikasis';

    protected $fillable = [
        'nama',
    ];

    public function mitras(): HasMany
    {
        return $this->hasMany(Mitra::class, 'klasifikasi_id');
    }
}
