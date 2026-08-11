<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniMitra extends Model
{
    use HasFactory;

    protected $table = 'alumni_mitras';

    protected $fillable = [
        'alumni_id',
        'mitra_id',
        'posisi',
        'tahun_mulai',
        'status',
        'sumber_data',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}
