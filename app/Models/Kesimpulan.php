<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kesimpulan extends Model
{
    use HasFactory;

    protected $table = 'kesimpulans';

    protected $fillable = ['id_kegiatan', 'ringkasan', 'saran', 'tindak_lanjut'];

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'id_kegiatan');
    }
}
