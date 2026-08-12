<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;

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

        $today = Carbon::today();
        
        // Ambil pimpinan untuk dinotifikasi
        $pimpinanUsers = User::whereHas('role', function($q) {
            $q->where('name', 'pimpinan');
        })->get();

        // Cari dokumen aktif
        $cooperations = Cooperation::where('status_berlaku', 'Aktif')
            ->whereNotNull('end_date')
            ->get();

        $count = 0;

        foreach ($cooperations as $coop) {
            $endDate = Carbon::parse($coop->end_date);
            $diffDays = $today->diffInDays($endDate, false); // false agar bisa negatif

            if (in_array($diffDays, [90, 60, 30])) {
                $count++;
                
                $title = "Peringatan Dini: Dokumen H-$diffDays";
                $message = "Dokumen kerjasama {$coop->jenis} dengan {$coop->mitra->nama_mitra} ({$coop->doc_number}) akan berakhir dalam $diffDays hari pada " . $endDate->format('d M Y') . ".";
                $link = route('pimpinan.kerjasama.show', $coop->id);

                // Kirim notifikasi ke semua pimpinan
                foreach ($pimpinanUsers as $pimpinan) {
                    Notifikasi::create([
                        'user_id' => $pimpinan->id,
                        'sender_id' => null, // Sistem
                        'cooperation_id' => $coop->id,
                        'type' => 'early_warning',
                        'title' => $title,
                        'message' => $message,
                        'link' => $link,
                        'is_read' => false,
                    ]);
                }

                // Jika dokumen diajukan oleh user tertentu (pic_id / user_id), kita bisa notifikasi ke mereka juga.
                if ($coop->user_id) {
                    Notifikasi::create([
                        'user_id' => $coop->user_id,
                        'sender_id' => null, // Sistem
                        'cooperation_id' => $coop->id,
                        'type' => 'early_warning',
                        'title' => $title,
                        'message' => $message,
                        'link' => route('kerjasama.show', $coop->id), // asumsikan rute general
                        'is_read' => false,
                    ]);
                }
                
                $this->info("Notifikasi H-$diffDays dibuat untuk dokumen {$coop->doc_number}");
            } elseif ($diffDays < 0) {
                // Jika dokumen sudah kadaluarsa tapi status masih 'Aktif'
                // Bisa otomatis mengubah status menjadi 'Kadaluarsa'
                $coop->status_berlaku = 'Kadaluarsa';
                $coop->save();

                $this->info("Dokumen {$coop->doc_number} otomatis diubah statusnya menjadi Kadaluarsa.");
            }
        }

        $this->info("Pengecekan selesai. {$count} dokumen mendekati masa kadaluarsa.");
    }
}
