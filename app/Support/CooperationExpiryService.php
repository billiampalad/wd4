<?php

namespace App\Support;

use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CooperationExpiryService
{
    /**
     * Jalankan pengecekan dokumen kerjasama yang telah berakhir masa berlakunya.
     * Otomatis ubah status_berlaku ke 'Kadaluarsa' dan kirimkan notifikasi
     * ke semua pihak terkait yang relevan (Humas, Jurusan & Prodi, UPA, Pusat, Pembuat, dan Mitra).
     */
    public static function checkAndNotifyExpiredCooperations(): int
    {
        $today = Carbon::today();

        // Cari dokumen yang end_date sudah lewat (end_date < today)
        // dan belum diperpanjang / belum dinonaktifkan
        $expiredCooperations = Cooperation::with([
            'mitra',
            'jurusans',
            'prodis',
            'upas',
            'pusats',
            'createdBy.role',
            'createdBy.profile',
        ])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', $today)
            ->whereNotIn(DB::raw("LOWER(COALESCE(status_berlaku, ''))"), [
                'dalam perpanjangan',
                'diperpanjang',
                'tidak aktif',
                'nonaktif',
                'non aktif',
            ])
            ->get();

        $processedCount = 0;

        foreach ($expiredCooperations as $coop) {
            // 1. Update status_berlaku di database menjadi 'Kadaluarsa' jika belum
            $currentStatus = strtolower(trim((string) $coop->status_berlaku));
            if (!in_array($currentStatus, ['kadaluarsa', 'kadarluarsa', 'kedaluwarsa'], true)) {
                $coop->status_berlaku = 'Kadaluarsa';
                $coop->saveQuietly();
            }

            // 2. Kirim notifikasi ke semua pihak terkait yang relevan
            self::notifyRelevantParties($coop);
            $processedCount++;
        }

        return $processedCount;
    }

    /**
     * Kirim notifikasi kepada semua pihak terkait yang relevan untuk sebuah kerjasama yang kadaluarsa.
     */
    public static function notifyRelevantParties(Cooperation $coop): void
    {
        $targetUserIds = collect(); // Map of userId => route/link
        $endDateStr = $coop->end_date ? Carbon::parse($coop->end_date)->format('d M Y') : 'tanggal yang ditentukan';
        $docTitle = $coop->judul ?: ($coop->title ?: 'Kerjasama Tanpa Judul');
        $docNumber = $coop->doc_number ? " (No: {$coop->doc_number})" : '';
        $mitraName = $coop->mitra?->nama_mitra ?? 'Mitra Terkait';

        // 1. PENGINPUT / PENANGGUNG JAWAB (created_by)
        if ($coop->created_by) {
            $creator = User::with('role', 'profile')->find($coop->created_by);
            if ($creator) {
                $targetUserIds->put($creator->id, self::resolveLinkForUser($creator, $coop));
            }
        }

        // 2. HUMAS / UNIT KERJA
        // Kirim ke seluruh user dengan role unit_kerja / humas
        $unitKerjaUsers = User::with('role', 'profile')
            ->whereHas('role', fn ($q) => $q->whereIn(DB::raw("LOWER(role_name)"), ['unit_kerja', 'humas']))
            ->get();
        foreach ($unitKerjaUsers as $user) {
            $targetUserIds->put($user->id, route('unit.kerjasama.show', $coop->id));
        }

        // 3. JURUSAN & PRODI TERKAIT
        $jurusanIds = collect();
        if ($coop->jurusan_id) {
            $jurusanIds->push((int) $coop->jurusan_id);
        }
        if ($coop->relationLoaded('jurusans')) {
            $jurusanIds = $jurusanIds->merge($coop->jurusans->pluck('id'));
        } else {
            $jurusanIds = $jurusanIds->merge($coop->jurusans()->pluck('jurusans.id'));
        }
        $jurusanIds = $jurusanIds->unique()->filter()->values();

        if ($jurusanIds->isNotEmpty()) {
            // User dengan role jurusan yang terhubung ke jurusan ini
            $jurusanUsers = User::with('role', 'profile')
                ->whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'jurusan'))
                ->whereHas('profile', fn ($q) => $q->whereIn('jurusan_id', $jurusanIds))
                ->get();
            foreach ($jurusanUsers as $user) {
                $targetUserIds->put($user->id, route('jurusan.kerjasama.show', $coop->id));
            }

            // User dengan role prodi yang bernaung di bawah jurusan ini
            $prodiUsersInJurusan = User::with('role', 'profile')
                ->whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'prodi'))
                ->whereHas('profile', fn ($q) => $q->whereIn('jurusan_id', $jurusanIds))
                ->get();
            foreach ($prodiUsersInJurusan as $user) {
                $targetUserIds->put($user->id, route('prodi.dkerjasama'));
            }
        }

        // Prodi terkait langsung via pivot prodis
        $prodiIds = collect();
        if ($coop->relationLoaded('prodis')) {
            $prodiIds = $prodiIds->merge($coop->prodis->pluck('id'));
        } else {
            $prodiIds = $prodiIds->merge($coop->prodis()->pluck('prodis.id'));
        }
        $prodiIds = $prodiIds->unique()->filter()->values();

        if ($prodiIds->isNotEmpty()) {
            $prodiUsers = User::with('role', 'profile')
                ->whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'prodi'))
                ->whereHas('profile', fn ($q) => $q->whereIn('prodi_id', $prodiIds))
                ->get();
            foreach ($prodiUsers as $user) {
                $targetUserIds->put($user->id, route('prodi.dkerjasama'));
            }
        }

        // 4. UPA TERKAIT
        $upaIds = collect();
        if ($coop->upa_id) {
            $upaIds->push((int) $coop->upa_id);
        }
        if ($coop->relationLoaded('upas')) {
            $upaIds = $upaIds->merge($coop->upas->pluck('id'));
        } else {
            $upaIds = $upaIds->merge($coop->upas()->pluck('upas.id'));
        }
        $upaIds = $upaIds->unique()->filter()->values();

        if ($upaIds->isNotEmpty()) {
            $upaUsers = User::with('role', 'profile')
                ->whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'upa'))
                ->whereHas('profile', fn ($q) => $q->whereIn('upa_id', $upaIds))
                ->get();
            foreach ($upaUsers as $user) {
                $targetUserIds->put($user->id, route('upa.kerjasama.show', $coop->id));
            }
        }

        // 5. PUSAT TERKAIT
        $pusatIds = collect();
        if ($coop->pusat_id) {
            $pusatIds->push((int) $coop->pusat_id);
        }
        if ($coop->relationLoaded('pusats')) {
            $pusatIds = $pusatIds->merge($coop->pusats->pluck('id'));
        } else {
            $pusatIds = $pusatIds->merge($coop->pusats()->pluck('pusats.id'));
        }
        $pusatIds = $pusatIds->unique()->filter()->values();

        if ($pusatIds->isNotEmpty()) {
            $pusatUsers = User::with('role', 'profile')
                ->whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'pusat'))
                ->whereHas('profile', fn ($q) => $q->whereIn('pusat_id', $pusatIds))
                ->get();
            foreach ($pusatUsers as $user) {
                $targetUserIds->put($user->id, route('pusat.kerjasama.show', $coop->id));
            }
        }

        // 6. MITRA PRIVAT TERKAIT (Hanya untuk dokumen milik mitra tersebut)
        $mitraUserIds = collect();
        if ($coop->mitra_id) {
            $mitraUsers = User::where('mitra_id', $coop->mitra_id)->get();
            foreach ($mitraUsers as $mUser) {
                $mitraUserIds->push($mUser->id);
                $targetUserIds->put($mUser->id, route('mitra.dokumen.show', $coop->id));
            }
        }

        // 7. PIMPINAN (Monitoring Dokumen)
        $pimpinanUsers = User::whereHas('role', fn ($q) => $q->where(DB::raw("LOWER(role_name)"), 'pimpinan'))->get();
        foreach ($pimpinanUsers as $pUser) {
            $targetUserIds->put($pUser->id, route('pimpinan.monitoring.detail', $coop->id));
        }

        // Simpan notifikasi secara unik (tidak dobel) untuk setiap pihak relevan
        foreach ($targetUserIds as $userId => $link) {
            $isMitra = $mitraUserIds->contains($userId);

            if ($isMitra) {
                $judul = "Masa Berlaku Kerja Sama Telah Berakhir";
                $pesan = "Masa berlaku kerja sama '{$docTitle}'{$docNumber} dengan Politeknik Negeri Manado telah berakhir pada {$endDateStr}.";
            } else {
                $judul = "Masa Berlaku Kerjasama Telah Berakhir";
                $pesan = "Masa berlaku kerjasama '{$docTitle}'{$docNumber} dengan {$mitraName} telah berakhir pada {$endDateStr}.";
            }

            Notifikasi::firstOrCreate([
                'user_id' => $userId,
                'source_id' => $coop->id,
                'source_type' => 'cooperation',
                'type' => 'kadaluarsa',
            ], [
                'sender_id' => null, // Sistem
                'title' => $judul,
                'message' => $pesan,
                'url' => $link,
                'is_read' => 0,
            ]);
        }
    }

    /**
     * Dapatkan link detail kerjasama yang sesuai dengan role pengguna.
     */
    private static function resolveLinkForUser(User $user, Cooperation $coop): string
    {
        $roleName = strtolower(str_replace([' ', '-'], '_', trim((string) ($user->role?->role_name ?? ''))));
        if ($roleName === 'humas') {
            $roleName = 'unit_kerja';
        }

        return match ($roleName) {
            'unit_kerja' => route('unit.kerjasama.show', $coop->id),
            'jurusan' => route('jurusan.kerjasama.show', $coop->id),
            'upa' => route('upa.kerjasama.show', $coop->id),
            'pusat' => route('pusat.kerjasama.show', $coop->id),
            'prodi' => route('prodi.dkerjasama'),
            'mitra' => route('mitra.dokumen.show', $coop->id),
            'pimpinan' => route('pimpinan.monitoring.detail', $coop->id),
            default => route('unit.kerjasama.show', $coop->id),
        };
    }
}
