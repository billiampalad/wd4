<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanFile extends Model
{
    protected $fillable = [
        'unit_kerja_id',
        'cooperation_id',
        'uploaded_by',
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

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
