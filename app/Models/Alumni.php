<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumnis';

    protected $fillable = [
        'nim',
        'nama',
        'prodi_id',
        'tahun_lulus',
        'email',
        'telepon',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function alumniMitras()
    {
        return $this->hasMany(AlumniMitra::class);
    }
}
