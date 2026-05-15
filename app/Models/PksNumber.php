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
    ];

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id');
    }
}
