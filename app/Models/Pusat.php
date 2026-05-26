<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pusat extends Model
{
    protected $fillable = ['nama_pusat'];

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'pusat_id');
    }
}
