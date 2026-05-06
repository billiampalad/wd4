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


# FUNGSI TOMBOL 'KIRIM KE PIMPINAN' & 'AJUKAN PERPANJANGAN'

1. Tombol "Kirim ke Pimpinan"
Tombol ini hanya muncul pada fase awal pengajuan atau saat ada perbaikan.

- Muncul jika: Status dokumen adalah 'Draft' (baru diunggah) atau 'Revisi' (dikembalikan oleh pimpinan untuk diperbaiki).
- Fungsi: Mengubah status_dokumen menjadi 'Menunggu Evaluasi' dan mengirimkan notifikasi ke Pimpinan.
- Hilang jika: Dokumen sudah dikirim ke Pimpinan atau sudah Disahkan. (Unit Kerja tidak perlu mengirim ulang dokumen yang sudah disetujui).

2. Tombol "Ajukan Perpanjangan"
Tombol ini khusus untuk fase akhir, saat dokumen sudah berjalan sah namun masa berlakunya hampir habis atau sudah habis.

- Muncul jika: Status dokumen sudah Disahkan dan status masa berlakunya adalah 'Kadaluarsa' (atau misalnya sisa 30 hari lagi sebelum kadaluarsa).
- Fungsi: Memulai proses perpanjangan, yang bisa mengubah status menjadi Dalam Perpanjangan dan mungkin memicu pembuatan form draft kesepakatan baru.
- Hilang jika: Dokumen masih berstatus Draft, Menunggu Evaluasi, atau jika masa aktifnya masih sangat panjang.

# FORM TAMBAHAN UNTUK AJUKAN PERPANJANGAN
Menggunakan Kembali Form "Input Data Baru"
Gunakan cara ini jika: Perpanjangan kerja sama dianggap sebagai kontrak baru yang memiliki Nomor Dokumen/PKS baru, tetapi masih berhubungan dengan kerja sama yang lama.
Anda tidak perlu membuat halaman HTML/Blade baru. Anda cukup menggunakan halaman Form Input Data Baru yang sudah Anda buat sebelumnya, tetapi dengan trik "Isi Otomatis" (Auto-fill).
- Cara Kerja UI:
Saat tombol "Ajukan Perpanjangan" ditekan, arahkan Unit Kerja ke halaman Form Input Data Baru. Namun, sistem akan mengisi otomatis semua kolomnya (Nama Mitra, Judul, Jurusan, dll) berdasarkan data kerja sama yang lama.
- Alur:
1. Sistem mengarahkan ke URL seperti: [domain.com/kerjasama/create?perpanjangan_dari=15](https://domain.com/kerjasama/create?perpanjangan_dari=15) (Angka 15 adalah ID kerja sama lama).
2. Unit Kerja melihat form input data yang sudah terisi, jadi mereka hanya perlu mengganti Nomor PKS baru dan menyesuaikan tanggal berlakunya.
3. Setelah di-submit, ini akan tersimpan sebagai Data Kerja Sama Baru dengan status awal Draft.
4. Kerja sama yang lama statusnya dibiarkan Kadaluarsa atau Selesai sebagai arsip sejarah.


# Kesimpulan Struktur Menu Final Pimpinan:
- Dashboard Eksekutif (Beranda & ringkasan tugas hari ini)
- Evaluasi & Validasi (Tempat Pimpinan memberi nilai, catatan, dan status persetujuan)
- Monitoring Data (Melihat grafik performa instansi dan peringatan dini)
- Arsip Digital (Pencarian cepat dokumen PDF yang sudah disahkan)
- Laporan Global (Pusat unduhan dan cetak rekapitulasi)


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