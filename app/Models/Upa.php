<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upa extends Model
{
    protected $fillable = ['nama_upa'];

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'upa_id');
    }
}
