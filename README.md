## Pengembangan dan Implementasi Sistem Informasi Berbasis Web untuk Evaluasi Kerja Sama Kampus–Industri (Studi Kasus: Politeknik Negeri Manado)

### Role Akses:
- Admin → full control
- Jurusan → Input kegiatan (Tidak ada Evaluasi)
- Pimpinan → Memberi penilaian & melakukan monitoring
- Unit → Input & Evaluasi

### Flow:
Jurusan / Unit Kerja
        ↓ (input data)
   Data Kerjasama
        ↓
   Notifikasi ke Pimpinan
        ↓
   Pimpinan Monitoring
        ↓
   Evaluasi & Catatan
        ↓
   Data Final + Laporan

### PATH FILE MENU JURUSAN
JURUSAN -> Fokus: INPUT DATA SAJA -> resources/views/auth/jurusan.blade.php
Dashboard -> resources/views/auth/layout/jurusan/dashboard.blade.php
Data Kerjasama -> resources/views/auth/layout/jurusan/dkerjasama.blade.php
Laporan Data -> resources/views/auth/layout/jurusan/laporan.blade.php

## PROSES CARA KERJA HAK AKSES UNIT KERJA DAN PIMPINAN
Berikut adalah penjelasan detail di mana proses "memberikan" dan "melihat" data tersebut terjadi:

1. Di mana Pimpinan MEMBERIKAN Ringkasan & Saran untuk Unit Kerja?
Proses ini dilakukan di dalam menu Evaluasi & Validasi pada hak akses Pimpinan.
Agar Pimpinan tidak bingung membedakan mana laporan Jurusan dan mana laporan Unit Kerja, Anda bisa membagi tampilan di menu ini menjadi dua Tab atau menggunakan Filter:

Tab Laporan Jurusan: Merender query data dengan status menunggu_evaluasi.

Tab Laporan Unit Kerja: Merender query data dengan status menunggu_validasi_pimpinan.

Ketika Pimpinan menekan tombol "Beri Penilaian" pada salah satu laporan Unit Kerja di tab tersebut, sistem akan memunculkan form di mana Pimpinan bisa mengetikkan Ringkasan Evaluasi dan Saran Tindak Lanjut, lalu mengubah statusnya menjadi Selesai.

2. Di mana Unit Kerja MELIHAT Hasil Ringkasan & Saran dari Pimpinan?
Setelah Pimpinan selesai melakukan evaluasi akhir, Unit Kerja dapat membaca hasilnya di dalam menu Data Kerjasama milik mereka (bukan di menu Evaluasi Internal).

Cara kerjanya:

Unit Kerja masuk ke menu Data Kerjasama.

Mereka mencari dokumen yang statusnya sudah Selesai.

Mereka menekan tombol "Detail" pada dokumen tersebut.

Di halaman detail tersebut (biasanya diletakkan di posisi paling bawah atau pada tab khusus bernama "Hasil Validasi Pimpinan"), sistem akan merender data teks Ringkasan Evaluasi, Saran Tindak Lanjut, dan Status Kelayakan yang telah diketik oleh Pimpinan.

3. Apa yang Diinput Unit Kerja di Menu "Evaluasi Internal"?
Sebagai pengingat, pada menu Evaluasi Internal milik Unit Kerja, mereka tidak membuat Ringkasan Evaluasi dan Saran Tindak Lanjut.

Di menu tersebut, Unit Kerja murni hanya melakukan Self-Assessment (mengisi metrik skoring form evaluasi) yang merujuk pada tabel evaluasis:

Kesesuaian dengan rencana (1-5)

Kualitas pelaksanaan (1-5)

Keterlibatan mitra (1-5)

Efisiensi penggunaan sumber daya (1-5)

Kepuasan pihak terkait (1-5)

Setelah Unit Kerja menyimpan nilai-nilai angka/skoring ini, barulah dokumen tersebut terlempar ke menu Pimpinan (menunggu_validasi_pimpinan) untuk diberikan kesimpulan akhir berupa teks (Ringkasan & Saran).

## PROSES CARA KERJA HAK AKSES JURUSAN DAN PIMPINAN
1. Untuk Hak Akses JURUSAN
A. Apa yang Diinput oleh Jurusan?
Jurusan tidak memiliki menu evaluasi. Mereka murni bekerja di menu Data Kerjasama.

Saat mengklik "Tambah Data", mereka hanya mengisi form fakta di lapangan (Informasi Umum, Tujuan, Pelaksanaan, Hasil, Kendala, dan Bukti Lampiran).

Mereka tidak melihat form skor angka (1-5) atau form teks ringkasan/saran.

Setelah selesai, mereka klik "Kirim ke Pimpinan" (Status berubah menjadi menunggu_evaluasi).

B. Di mana Jurusan MELIHAT Penilaian, Ringkasan, dan Saran?
Sama seperti Unit Kerja, Jurusan melihat hasil akhirnya di dalam menu Data Kerjasama.

Saat sebuah dokumen sudah berstatus Selesai, Jurusan mengklik tombol "Detail".

Di bagian paling bawah halaman detail tersebut (misalnya di dalam card bernama "Hasil Penilaian Pimpinan"), Jurusan dapat melihat lengkap:

Skor Kinerja (1-5) yang diberikan Pimpinan.

Ringkasan Evaluasi (Teks).

Saran Tindak Lanjut (Teks).

2. Untuk Hak Akses PIMPINAN
Karena Pimpinan menerima dua jenis laporan yang berbeda (dari Jurusan dan dari Unit Kerja), maka form yang muncul di layar Pimpinan akan sedikit berbeda (dinamis) menyesuaikan siapa pengirimnya.

A. Di mana Pimpinan MEMBERIKAN Penilaian & Catatan?
Semuanya terpusat di menu Evaluasi & Validasi. Di menu ini, sistem akan merender form yang berbeda tergantung status dokumen:

Skenario 1: Mengevaluasi Laporan JURUSAN (Status: menunggu_evaluasi)
Saat Pimpinan klik "Beri Penilaian" pada data milik Jurusan, form yang terbuka akan sangat lengkap. Pimpinan harus menginput:

Skor Angka (1-5) (Kesesuaian, Kualitas, Keterlibatan, Efisiensi, Kepuasan).

Ringkasan Evaluasi (Input Textarea).

Saran Tindak Lanjut (Input Textarea).

Status Validasi Akhir (Layak/Tidak).

Skenario 2: Memvalidasi Laporan UNIT KERJA (Status: menunggu_validasi_pimpinan)
Saat Pimpinan klik "Beri Penilaian" pada data milik Unit Kerja, form yang terbuka lebih ringkas.

Pimpinan akan melihat Skor Angka (1-5) yang sudah diisi secara Self-Assessment oleh Unit Kerja (tampilannya Read-Only, Pimpinan tinggal melihat saja).

Pimpinan hanya perlu menginput:

Ringkasan Evaluasi (Input Textarea).

Saran Tindak Lanjut (Input Textarea).

Status Validasi Akhir (Layak/Tidak).

B. Di mana Pimpinan MELIHAT hasil kerjanya?
Setelah mengeklik "Simpan", dokumen tersebut hilang dari antrean menu Evaluasi & Validasi. Jika Pimpinan ingin melihat kembali dokumen yang sudah selesai mereka nilai, mereka bisa membukanya di menu Monitoring Data atau menarik datanya di menu Laporan Global.

Dengan logika render form yang dinamis di Controller Pimpinan ini, tugas Pimpinan menjadi sangat efisien dan tidak ada form yang tumpang tindih.

Untuk hak 2 tap pada menu "Evaluasi & Validasi" di hak akses di pimpinan yaitu yang pertama:
1. Evaluasi Jurusan
menggunakan tabel databees dari evaluasis dan kesimpulans.
2. Evaluasi Unit Kerja
hanya menggunakan tabel databeses dari kesipulans saja.

## PERAN KERJA NOTIFIKASI
Gemini berkata
Keputusan yang sangat jeli! Memiliki dua akses untuk menu yang sama (di header dan di sidebar) seringkali membuat tampilan menjadi redundan (tumpang tindih).

Pendekatan terbaik dan paling modern untuk sistem informasi seperti ini adalah menghapus menu Notifikasi dari sidebar dan memusatkan semuanya pada ikon lonceng di header.

Dengan cara ini, sidebar Anda akan benar-benar bersih dan hanya berisi menu inti (Dashboard & Data Kerjasama).

Berikut adalah skenario penerapan UI/UX dan alur sistem yang paling ideal untuk fitur notifikasi Anda:

1. Peran Ikon Lonceng di Header (Quick View)
Ikon di header berfungsi sebagai pusat perhatian instan karena posisinya selalu terlihat di halaman mana pun user berada.

Badge Angka: Menampilkan jumlah notifikasi yang belum dibaca (berdasarkan kolom is_read = 0 di tabel notifikasis).

Dropdown Pop-up: Saat ikon diklik, muncul dropdown yang menampilkan 3 hingga 5 notifikasi terbaru.

Aksi Cepat: User bisa mengklik salah satu notifikasi di dropdown tersebut, dan sistem akan langsung mengarahkan mereka ke halaman detail kegiatan yang bersangkutan, sekaligus mengubah status is_read menjadi 1 (sudah dibaca).