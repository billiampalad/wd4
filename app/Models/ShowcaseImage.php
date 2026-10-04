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
        'image_path',
    ];

    /**
     * Accessor agar $model->image tetap mengembalikan path image_path
     */
    public function getImageAttribute()
    {
        return $this->image_path;
    }
}
