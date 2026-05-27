<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanFile extends Model
{
    protected $fillable = [
        'unit_kerja_id',
        'jurusan_id',
        'upa_id',
        'pusat_id',
        'cooperation_id',
        'uploaded_by',
        'uploader_role',
        'file_path',
        'original_name',
        'file_size',
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(\App\Models\UnitKerja::class, 'unit_kerja_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(\App\Models\Jurusan::class, 'jurusan_id');
    }

    public function upa()
    {
        return $this->belongsTo(\App\Models\Upa::class, 'upa_id');
    }

    public function pusat()
    {
        return $this->belongsTo(\App\Models\Pusat::class, 'pusat_id');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
