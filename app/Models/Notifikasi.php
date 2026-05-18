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
        'source_type',
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

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class, 'source_id');
    }

    public function pengajuanKerjasamaMitra()
    {
        return $this->belongsTo(PengajuanKerjasamaMitra::class, 'source_id');
    }

    public static function send($userId, $senderId, $sourceId, $type, $judul, $pesan, $link, $sourceType = 'cooperation')
    {
        return self::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'source_id' => $sourceId,
            'source_type' => $sourceId ? $sourceType : null,
            'type' => $type,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'is_read' => 0
        ]);
    }
}
