<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasis';

    protected $fillable = [
        'cooperation_id',
        'evaluator_id',
        'tipe_evaluasi',
        'score',
        'realisasi_volume',
        'realisasi_output',
        'realisasi_outcome',
        'sesuai_rencana',
        'kualitas',
        'keterlibatan',
        'efisiensi',
        'kepuasan',
        'kendala',
        'ringkasan',
        'rekomendasi',
        'kesimpulan',
        'tindak_lanjut',
        'status_validasi',
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
