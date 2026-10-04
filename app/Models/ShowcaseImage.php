<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowcaseImage extends Model
{
    use HasFactory;

    protected $table = 'showcase_images';

    protected $fillable = [
        'judul',
        'kategori',
        'label_nav',
        'deskripsi',
        'lokasi_spesifik',
        'highlight_meta',
        'tags',
        'image_path',
        'urutan',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Accessor agar $model->image tetap mengembalikan path image_path
     */
    public function getImageAttribute()
    {
        return $this->image_path;
    }
}
