<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckExpiredCooperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifikasi:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dokumen kerjasama yang akan kedaluwarsa dan kirim notifikasi Early Warning System (H-90, H-60, H-30)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan kedaluwarsa dokumen kerjasama...');

        // 1. Proses kerjasama yang sudah berakhir dan kirim notifikasi ke semua unit terkait dan mitra
        $expiredCount = \App\Support\CooperationExpiryService::checkAndNotifyExpiredCooperations();
        $this->info("Pengecekan kadaluarsa selesai. {$expiredCount} dokumen diproses.");

        // 2. Pengecekan Peringatan Dini (Early Warning H-90, H-60, H-30) untuk Pimpinan
        $today = Carbon::today();
        $pimpinanUsers = User::whereHas('role', function($q) {
            $q->where(DB::raw("LOWER(role_name)"), 'pimpinan');
        })->get();

        $activeCooperations = Cooperation::with('mitra')
            ->where('status_berlaku', 'Aktif')
            ->whereNotNull('end_date')
            ->get();

        $warningCount = 0;

        foreach ($activeCooperations as $coop) {
            $endDate = Carbon::parse($coop->end_date);
            $diffDays = (int) $today->diffInDays($endDate, false);

            if (in_array($diffDays, [90, 60, 30], true)) {
                $warningCount++;
                $mitraName = $coop->mitra?->nama_mitra ?? 'Mitra Terkait';
                $title = "Peringatan Dini: Dokumen H-$diffDays";
                $message = "Dokumen kerjasama {$coop->jenis} dengan {$mitraName} ({$coop->doc_number}) akan berakhir dalam $diffDays hari pada " . $endDate->format('d M Y') . ".";
                $link = route('pimpinan.monitoring.detail', $coop->id);

                foreach ($pimpinanUsers as $pimpinan) {
                    Notifikasi::firstOrCreate([
                        'user_id' => $pimpinan->id,
                        'source_id' => $coop->id,
                        'source_type' => 'cooperation',
                        'type' => "warning_h_{$diffDays}",
                    ], [
                        'sender_id' => null,
                        'title' => $title,
                        'message' => $message,
                        'url' => $link,
                        'is_read' => 0,
                    ]);
                }

                $this->info("Notifikasi H-$diffDays dibuat untuk dokumen {$coop->doc_number}");
            }
        }

        $this->info("Pengecekan selesai. {$warningCount} dokumen mendekati masa kadaluarsa.");
    }
}
