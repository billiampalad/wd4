<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jabatan',
        'jurusan_id',
        'unit_kerja_id',
        'upa_id',
        'pusat_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function upa()
    {
        return $this->belongsTo(Upa::class, 'upa_id');
    }

    public function pusat()
    {
        return $this->belongsTo(Pusat::class, 'pusat_id');
    }
}
