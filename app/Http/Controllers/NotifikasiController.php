<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Ambil notifikasi terbaru untuk user saat ini.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil notifikasi yang belum dibaca atau sudah dibaca tapi tetap tampil
        // Sesuai permintaan: "ketika data tersebut sudah di kerjakan maka data notif tersebut akan hilang"
        // Jadi kita filter berdasarkan status kegiatan_kerjasamas jika itu notifikasi pimpinan
        
        $query = Notifikasi::with([
            'sender.profile.jurusan',
            'sender.profile.unitKerja',
            'kegiatanKerjasama.jurusans',
            'kegiatanKerjasama.unitKerjas',
        ])
            ->where('user_id', $user->id)
            ->where('is_read', 0) // Hanya tampilkan yang belum dibaca agar bisa "hilang" saat ditandai dibaca
            ->latest();

        // Filter untuk Pimpinan: Hanya tampilkan jika kegiatan masih butuh aksi
        if ($user->role && $user->role->role_name === 'pimpinan') {
            $query->whereHas('kegiatanKerjasama', function($q) {
                $q->whereIn('status', ['menunggu_evaluasi', 'menunggu_validasi']);
            });
        }

        $notifikasis = $query->take(10)->get();
        $unreadCount = $query->clone()->where('is_read', 0)->count();

        return response()->json([
            'success' => true,
            'data' => $notifikasis,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())->findOrFail($id);
        $notifikasi->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca.'
        ]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        Notifikasi::where('user_id', Auth::id())->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai dibaca.'
        ]);
    }
}
