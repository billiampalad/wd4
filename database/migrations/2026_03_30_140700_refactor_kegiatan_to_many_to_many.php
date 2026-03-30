<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Refactor: Ubah relasi id_jenis, id_jurusan, id_unit dari one-to-one
     * menjadi many-to-many menggunakan pivot table.
     * Data yang sudah ada akan di-migrasi ke pivot table sebelum kolom lama dihapus.
     */
    public function up(): void
    {
        // ─── 1. Buat pivot table: kegiatan_jenis_kerjasamas ──────────
        Schema::create('kegiatan_jenis_kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->constrained('kegiatan_kerjasamas')->cascadeOnDelete();
            $table->foreignId('id_jenis')->constrained('jenis_kerjasamas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_kegiatan', 'id_jenis']); // Cegah duplikasi
        });

        // ─── 2. Buat pivot table: kegiatan_jurusans ──────────────────
        Schema::create('kegiatan_jurusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->constrained('kegiatan_kerjasamas')->cascadeOnDelete();
            $table->foreignId('id_jurusan')->constrained('jurusans')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_kegiatan', 'id_jurusan']); // Cegah duplikasi
        });

        // ─── 3. Buat pivot table: kegiatan_units ─────────────────────
        Schema::create('kegiatan_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->constrained('kegiatan_kerjasamas')->cascadeOnDelete();
            $table->foreignId('id_unit')->constrained('unit_kerjas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_kegiatan', 'id_unit']); // Cegah duplikasi
        });

        // ─── 4. Migrasi data existing ke pivot tables ────────────────

        // Migrasi id_jenis → kegiatan_jenis_kerjasamas
        $kegiatanWithJenis = DB::table('kegiatan_kerjasamas')
            ->whereNotNull('id_jenis')
            ->select('id', 'id_jenis')
            ->get();

        foreach ($kegiatanWithJenis as $row) {
            DB::table('kegiatan_jenis_kerjasamas')->insert([
                'id_kegiatan' => $row->id,
                'id_jenis'    => $row->id_jenis,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Migrasi id_jurusan → kegiatan_jurusans
        $kegiatanWithJurusan = DB::table('kegiatan_kerjasamas')
            ->whereNotNull('id_jurusan')
            ->select('id', 'id_jurusan')
            ->get();

        foreach ($kegiatanWithJurusan as $row) {
            DB::table('kegiatan_jurusans')->insert([
                'id_kegiatan' => $row->id,
                'id_jurusan'  => $row->id_jurusan,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Migrasi id_unit → kegiatan_units
        $kegiatanWithUnit = DB::table('kegiatan_kerjasamas')
            ->whereNotNull('id_unit')
            ->select('id', 'id_unit')
            ->get();

        foreach ($kegiatanWithUnit as $row) {
            DB::table('kegiatan_units')->insert([
                'id_kegiatan' => $row->id,
                'id_unit'     => $row->id_unit,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── 5. Hapus kolom lama dari kegiatan_kerjasamas ────────────
        Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            // Drop foreign key constraints dulu, lalu drop kolom
            $table->dropForeign(['id_jenis']);
            $table->dropColumn('id_jenis');

            $table->dropForeign(['id_jurusan']);
            $table->dropColumn('id_jurusan');

            $table->dropForeign(['id_unit']);
            $table->dropColumn('id_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ─── 1. Tambah kembali kolom lama ────────────────────────────
        Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->foreignId('id_jenis')->nullable()->after('nama_kegiatan')->constrained('jenis_kerjasamas');
            $table->foreignId('id_jurusan')->nullable()->after('id_jenis')->constrained('jurusans');
            $table->foreignId('id_unit')->nullable()->after('id_jurusan')->constrained('unit_kerjas');
        });

        // ─── 2. Restore data dari pivot tables ───────────────────────

        // Restore id_jenis (ambil jenis pertama saja, karena rollback ke single)
        $jenisData = DB::table('kegiatan_jenis_kerjasamas')
            ->select('id_kegiatan', DB::raw('MIN(id_jenis) as id_jenis'))
            ->groupBy('id_kegiatan')
            ->get();

        foreach ($jenisData as $row) {
            DB::table('kegiatan_kerjasamas')
                ->where('id', $row->id_kegiatan)
                ->update(['id_jenis' => $row->id_jenis]);
        }

        // Restore id_jurusan (ambil jurusan pertama)
        $jurusanData = DB::table('kegiatan_jurusans')
            ->select('id_kegiatan', DB::raw('MIN(id_jurusan) as id_jurusan'))
            ->groupBy('id_kegiatan')
            ->get();

        foreach ($jurusanData as $row) {
            DB::table('kegiatan_kerjasamas')
                ->where('id', $row->id_kegiatan)
                ->update(['id_jurusan' => $row->id_jurusan]);
        }

        // Restore id_unit (ambil unit pertama)
        $unitData = DB::table('kegiatan_units')
            ->select('id_kegiatan', DB::raw('MIN(id_unit) as id_unit'))
            ->groupBy('id_kegiatan')
            ->get();

        foreach ($unitData as $row) {
            DB::table('kegiatan_kerjasamas')
                ->where('id', $row->id_kegiatan)
                ->update(['id_unit' => $row->id_unit]);
        }

        // ─── 3. Drop pivot tables ────────────────────────────────────
        Schema::dropIfExists('kegiatan_units');
        Schema::dropIfExists('kegiatan_jurusans');
        Schema::dropIfExists('kegiatan_jenis_kerjasamas');
    }
};
