<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerjas';
    protected $fillable = ['nama_unit_pelaksana'];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
