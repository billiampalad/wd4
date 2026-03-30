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