<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Cooperation;
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

        // 1. Inisialisasi query dasar untuk notifikasi yang belum dibaca milik user ini
        $query = Notifikasi::where('user_id', $user->id)
            ->where('is_read', 0);

        // 2. Filter khusus untuk Pimpinan: Hanya tampilkan jika kegiatan masih butuh aksi
        $roleName = strtolower($user->role->role_name ?? '');
        if ($roleName === 'pimpinan') {
            $query->where(function ($q) {
                // Relasi ke model baru (Cooperation)
                $q->whereHas('cooperation', function ($sq) {
                    $sq->where('status_dokumen', 'Menunggu Evaluasi');
                })
                    // Notifikasi sistem tanpa source_id
                    ->orWhereNull('source_id')
                    // Fallback berdasarkan tipe
                    ->orWhereIn('type', ['evaluasi', 'revisi', 'sudah_revisi']);
            });
        }

        // 3. Hitung jumlah total unread count untuk badge
        $unreadCount = $query->count();

        // 4. Ambil 10 data terbaru dengan eager loading untuk performa
        $notifikasis = $query->with([
            'sender.profile.jurusan',
            'sender.profile.unitKerja',
            'cooperation.jurusans',
            'cooperation.upas',
            'cooperation.pusats',
        ])
            ->latest()
            ->take(10)
            ->get();

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
