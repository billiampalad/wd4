<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PksNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperation_id',
        'number',
        'sort_order',
        'nomor_pihak_kampus',
        'nomor_pihak_mitra',
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }

    public function getNumberAttribute($value)
    {
        return $value ?: ($this->nomor_pihak_kampus ?: $this->nomor_pihak_mitra);
    }

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = $value;
        if (empty($this->attributes['nomor_pihak_kampus'])) {
            $this->attributes['nomor_pihak_kampus'] = $value;
        }
    }
}
