<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JenisKerjasama;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('jenis_kerjasamas')) {
            Schema::create('jenis_kerjasamas', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 255);
                $table->string('nama_kerjasama', 255)->nullable();
                $table->timestamps();
            });
        }

        $items = [
            1 => 'Pelatihan',
            2 => 'Pemagangan',
            3 => 'Penelitian Bersama',
            4 => 'Penelitian Bersama - Artikel/Jurnal ilmiah',
            5 => 'Pelatihan Bersama - Paten',
            6 => 'Asistensi Mengajar di Satuan Pendidikan-Kampus Merdeka',
            7 => 'Gelar Bersama (Joint Degree)',
            8 => 'Gelar Ganda (Dual Degree)',
            9 => 'Kegiatan Wirausaha-Kampus Merdeka',
            10 => 'Magang/Praktik Kerja-Kampus Merdeka',
            11 => 'Membangun Desa/KKN Tematik-Kampus Merdeka',
            12 => 'Penelitian Bersama - Prototipe',
            13 => 'Penelitian/Riset-Kampus Merdeka',
            14 => 'Penerbitan Berkala Ilmiah',
            15 => 'Pengabdian Kepada Masyarakat',
            16 => 'Pengembangan Kurikulum/Program Bersama',
            17 => 'Pengembangan Pusat Penelitian dan Pengembangan Keilmuan',
            18 => 'Pengembangan Sistem/Produk',
            19 => 'Pengiriman Praktisi sebagai Dosen',
            20 => 'Penyaluran Lulusan',
            21 => 'Penyelenggaraan Seminar/Konferensi Ilmiah',
            22 => 'Pertukaran Dosen',
            23 => 'Pertukaran Mahasiswa',
            24 => 'Pertukaran Pelajar-Kampus Merdeka',
            25 => 'Proyek kemanusiaan-Kampus Merdeka',
            26 => 'Studi/Proyek Independen-Kampus Merdeka',
            27 => 'Transfer Kredit',
            28 => 'Visiting Professor',
        ];

        foreach ($items as $id => $nama) {
            try {
                $exists = DB::table('jenis_kerjasamas')->where('id', $id)->first();
                if (!$exists) {
                    DB::table('jenis_kerjasamas')->insert([
                        'id' => $id,
                        'nama' => $nama,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Update nama jika masih kosong
                    if (empty($exists->nama)) {
                        DB::table('jenis_kerjasamas')->where('id', $id)->update([
                            'nama' => $nama,
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore query error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to preserve production data
    }
};
