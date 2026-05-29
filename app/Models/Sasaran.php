<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sasaran extends Model
{
    //
    protected $fillable = [
        'deskripsi',
    ];

    public function indikators()
    {
        return $this->hasMany(Indikator::class);
    }
}
