<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $fillable = [
        'jurusan_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
}
