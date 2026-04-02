<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'sender_id',
        'source_id',
        'type',
        'judul',
        'pesan',
        'link',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function kegiatanKerjasama()
    {
        return $this->belongsTo(KegiatanKerjasama::class, 'source_id');
    }

    public static function send($userId, $senderId, $sourceId, $type, $judul, $pesan, $link)
    {
        return self::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'source_id' => $sourceId,
            'type' => $type,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'is_read' => 0
        ]);
    }
}
