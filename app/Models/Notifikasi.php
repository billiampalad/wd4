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
        'title',
        'message',
        'url',
        'is_read',
    ];

    protected $appends = [
        'pesan',
        'judul',
        'link',
    ];

    public function getPesanAttribute(): ?string
    {
        return $this->attributes['message'] ?? ($this->attributes['pesan'] ?? null);
    }

    public function getJudulAttribute(): ?string
    {
        return $this->attributes['title'] ?? ($this->attributes['judul'] ?? null);
    }

    public function getLinkAttribute(): ?string
    {
        return $this->attributes['url'] ?? ($this->attributes['link'] ?? null);
    }


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

    public function pengajuanKerjasamaBaru()
    {
        return $this->belongsTo(PengajuanKerjasamaBaru::class, 'source_id');
    }

    public function pengajuanPerpanjanganKerjasama()
    {
        return $this->belongsTo(PengajuanPerpanjanganKerjasama::class, 'source_id');
    }

    public static function send($userId, $senderId, $sourceId, $type, $judul, $pesan, $link, $sourceType = 'cooperation')
    {
        return self::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'source_id' => $sourceId,
            'source_type' => $sourceId ? $sourceType : null,
            'type' => $type,
            'title' => $judul,
            'message' => $pesan,
            'url' => $link,
            'is_read' => 0
        ]);
    }
}
