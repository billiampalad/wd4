<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BantuanController extends Controller
{
    /**
     * Tampilkan halaman formulir Hubungi Administrator (UC35) beserta riwayat pesan.
     */
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        // Ambil riwayat pesan yang telah dikirim mitra ke administrator
        $sentMessages = Notifikasi::where('sender_id', $user->id)
            ->where('type', 'pesan_mitra')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('auth.mitra', [
            'content' => 'auth.layout.mitra.bantuan',
            'sentMessages' => $sentMessages,
            'mitra' => $mitra,
        ]);
    }

    /**
     * Simpan pesan/tiket bantuan dari Mitra dan distribusikan notifikasi ke Administrator.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subjek' => 'required|string|max:255',
            'kategori' => 'required|in:Kendala Teknis,Pertanyaan Umum,Permintaan Bantuan',
            'pesan' => 'required|string|min:5',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ], [
            'subjek.required' => 'Subjek pesan wajib diisi.',
            'kategori.required' => 'Kategori pesan wajib dipilih.',
            'kategori.in' => 'Kategori yang dipilih tidak valid.',
            'pesan.required' => 'Isi pesan wajib diisi.',
            'pesan.min' => 'Isi pesan minimal 5 karakter.',
            'lampiran.mimes' => 'Format file lampiran harus PDF, JPG, PNG, atau DOCX.',
            'lampiran.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        $user = Auth::user();
        $mitraName = $user->mitra?->nama_mitra ?? $user->name;

        // Proses upload lampiran jika ada
        $attachmentPath = null;
        if ($request->hasFile('lampiran')) {
            $attachmentPath = $request->file('lampiran')->store('lampiran_bantuan', 'public');
        }

        $lampiranInfo = $attachmentPath ? " [Lampiran: " . basename($attachmentPath) . "]" : "";

        // Dapatkan semua user Admin untuk menerima notifikasi pesan masuk
        $adminUsers = User::whereHas('role', function ($q) {
            $q->where(DB::raw("LOWER(role_name)"), 'admin');
        })->get();

        $adminTitle = "Pesan Mitra ({$request->kategori}): {$request->subjek} - {$mitraName}";
        $adminMessage = "Mitra {$mitraName} mengirim pesan bantuan kategori {$request->kategori}:\n\"{$request->pesan}\"" . $lampiranInfo;

        foreach ($adminUsers as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'sender_id' => $user->id,
                'source_id' => $user->mitra_id,
                'source_type' => 'mitra',
                'type' => 'pesan_mitra',
                'title' => $adminTitle,
                'message' => $adminMessage,
                'url' => route('mitra.index'),
                'is_read' => 0,
            ]);
        }

        // Catat arsip riwayat pengiriman untuk Mitra sendiri
        Notifikasi::create([
            'user_id' => $user->id,
            'sender_id' => $user->id,
            'source_id' => $user->mitra_id,
            'source_type' => 'mitra',
            'type' => 'pesan_mitra',
            'title' => "[{$request->kategori}] {$request->subjek}",
            'message' => $request->pesan . $lampiranInfo,
            'url' => route('mitra.bantuan.index'),
            'is_read' => 1,
        ]);

        return redirect()->route('mitra.bantuan.index')
            ->with('success', 'Pesan Anda Telah Terkirim. Administrator kami akan segera menindaklanjuti pesan Anda.');
    }
}
