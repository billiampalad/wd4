<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = ['kode_jurusan', 'nama_jurusan'];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function prodis()
    {
        return $this->hasMany(Prodi::class);
    }
}
