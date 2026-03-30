<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = ['nama_jurusan'];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
