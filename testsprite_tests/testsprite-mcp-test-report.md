# TestSprite Testing Report — Subsistem Master Data, Dokumen KS, Pengajuan, Kegiatan, Monitoring, Evaluasi, Laporan & Dashboard (UC01 s.d. UC31)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** 
  - [analysis-flowchart.md (L51–L145: Subsistem Autentikasi)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L51-L145)
  - [analysis-flowchart.md (L147–L464: Subsistem Master Data UC01–UC07)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L147-L464)
  - [analysis-flowchart.md (L466–L714: Subsistem Dokumen Kerja Sama UC08–UC14)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L466-L714)
  - [analysis-flowchart.md (L716–L863: Subsistem Pengajuan Kerja Sama UC15–UC18)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L716-L863)
  - [analysis-flowchart.md (L865–L1127: Subsistem Kegiatan, Monitoring & Evaluasi UC19–UC26)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L865-L1127)
  - [analysis-flowchart.md (L1129–L1269: Subsistem Laporan dan Dashboard UC27–UC31)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L1129-L1269)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-19
- **Prepared by:** TestSprite AI Testing Agent & Antigravity QA Team
- **Akun Pengujian:**
  - **Admin:** `admin@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/admin/login`)
  - **Pimpinan:** `pimpinan@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Humas:** `humas@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Jurusan:** `jurusan@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **UPA:** `upa@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Pusat:** `pusat@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Mitra:** `mitra@industri.co.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Prodi:** `prodi@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
- **Overall Status:**
  - **Backend Test Suite (UC27–UC31):** ✅ **100% Passed (5/5 Scenarios Passed)**
  - **Frontend E2E Suite (Playwright UC27–UC31):** ✅ **100% Passed (5/5 Scenarios Passed)**
  - **Total Test Cases in TestSprite:** **34 Test Cases (100% PASSED)**

---

## 2️⃣ Hasil Pengujian Subsistem Laporan dan Dashboard (UC27 s.d. UC31)

### 📋 8.1 UC27 — Melihat Dashboard Eksekutif
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC032` / `test_backend_uc27_uc31.php` & `verify_e2e_uc27_uc31.py`
- **Route & Controller:** `GET /pimpinan` (`DashboardController@pimpinan`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Pimpinan login via `http://127.0.0.1:8000/login`.
  2. Sistem memuat dashboard eksekutif dengan widget KPI utama: Total KS Aktif (16), Total Mitra (26), Nilai Kontrak, dan Mahasiswa Magang Aktif (17).
  3. Visualisasi grafik analitik lengkap: Distribusi Jenis KS (MoU/MoA/IA), Tren Tahunan, Top Jurusan Kolaboratif, dan Klasifikasi Mitra.
  4. Ringkasan operasional: Dokumen mendekati masa kedaluwarsa (30/60 hari), dalam perpanjangan, dan antrean validasi evaluasi unit.

---

### 📋 8.2 UC28 — Melihat Dashboard Per-Unit
- **Aktor:** Humas, Jurusan, Prodi, UPA, Pusat
- **Test ID / Code:** `TC033` / `test_backend_uc27_uc31.php` & `verify_e2e_uc27_uc31.py`
- **Route & Controller:** `GET /unit`, `GET /jurusan`, `GET /prodi`, `GET /upa`, `GET /pusat`
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. **Jurusan:** Memuat dashboard jurusan dengan metrik kerja sama spesifik jurusan, draf dokumen, dan antrean evaluasi.
  2. **Prodi:** Memuat ringkasan mahasiswa magang aktif (11 peserta), status distribusi penempatan, penyerapan alumni bekerja (80%), dan tren penempatan.
  3. **UPA:** Memuat ringkasan kerja sama spesifik UPA (misal UPA Perpustakaan/Laboratorium) dan status keaktifan dokumen.
  4. **Pusat:** Memuat kerja sama spesifik Pusat Penelitian & Pengabdian Masyarakat (P3M) dan capaian sasaran.
  5. **Humas / Unit Kerja:** Memuat statistik operasional publikasi, galeri showcase, dan kemitraan umum.

---

### 📋 8.3 UC29 — Melihat Dashboard Mitra
- **Aktor:** Mitra Industri
- **Test ID / Code:** `TC034` / `test_backend_uc27_uc31.php` & `verify_e2e_uc27_uc31.py`
- **Route & Controller:** `GET /mitra` (`DashboardController@mitra`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Mitra login via portal `http://127.0.0.1:8000/login`.
  2. Menampilkan ringkasan kemitraan khusus instansi mitra: Dokumen Aktif (6 dokumen), Dokumen Menunggu Review, dan Mahasiswa yang Sedang Magang.
  3. Menampilkan riwayat dokumen kerja sama terbaru beserta tautan berkas dan status masa berlaku.
  4. Menyediakan tombol aksi cepat (*Quick Actions*): Ajukan Kerja Sama Baru, Ajukan Perpanjangan, Input Penilaian Magang, dan Beri Umpan Balik CSAT.

---

### 📋 8.4 UC30 — Mengekspor Laporan
- **Aktor:** Admin, Pimpinan, Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC035` / `test_backend_uc27_uc31.php` & `verify_e2e_uc27_uc31.py`
- **Route & Controller:** `GET /admin/dashboard`, `GET /pimpinan/laporan`, `GET /pimpinan/laporan/preview`, `GET /pimpinan/laporan/pdf`, `GET /pimpinan/laporan/excel`
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. **Admin Panel:** Admin login melalui `http://127.0.0.1:8000/admin/login` dengan form otentikasi administrator terisolasi, berhasil masuk ke panel admin.
  2. **Preview Laporan:** Pimpinan dan unit membuka halaman laporan, menentukan parameter filter (rentang tanggal, status berlaku, jenis dokumen MoU/MoA/IA, unit pelaksana). AJAX preview memuat data secara instan.
  3. **Export PDF:** Mengunduh berkas laporan format PDF landscape dengan layout tabel komprehensif (HTTP 200).
  4. **Export Excel / CSV:** Mengunduh berkas spreadsheet terstruktur dengan seluruh kolom atribut kerja sama (HTTP 200).

---

### 📋 8.5 UC31 — Melihat Analitik
- **Aktor:** Admin, Pimpinan, Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC036` / `test_backend_uc27_uc31.php` & `verify_e2e_uc27_uc31.py`
- **Route & Controller:** `GET /{role}/analitik/status-kerjasama`, `GET /{role}/analitik/klasifikasi-mitra`, `GET /{role}/analitik/geo-mitra`
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. **Analitik Status KS:** Menampilkan visualisasi persentase status dokumen (Aktif, Kedaluwarsa, Dalam Perpanjangan) dan rata-rata durasi keaktifan.
  2. **Analitik Klasifikasi Mitra:** Menampilkan grafik distribusi kemitraan berdasarkan klasifikasi (Dunia Usaha/Industri, Instansi Pemerintah, BUMN, Perguruan Tinggi).
  3. **Analitik Geografis Mitra:** Menampilkan peta interaktif dan konsentrasi sebaran kemitraan tingkat wilayah/nasional/internasional berdasarkan koordinat latitude & longitude mitra.

---

## 3️⃣ Matriks Lengkap Kasus Uji TestSprite (34 Test Cases)

| Test ID | Skenario Pengujian | Aktor | Tipe | Status |
|---|---|---|---|---|
| **TC001** | Mitra mengajukan kerja sama baru melalui portal mitra (UC15) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC002** | Public user can submit a partnership application (UC15) | Publik / Mitra | Frontend Playwright | ✅ **PASSED** |
| **TC003** | Masuk ke dashboard sesuai peran | Multi-Role | Frontend Playwright | ✅ **PASSED** |
| **TC004** | Admin can sign in and reach the dashboard | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC005** | Pimpinan menerima dan memvalidasi pengajuan mitra (UC16, UC17) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC006** | Leader can approve and evaluate documents (UC11, UC12) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC007** | Mengirim draft dokumen ke pimpinan untuk evaluasi (UC10) | Jurusan / Unit | Frontend / Backend | ✅ **PASSED** |
| **TC008** | Keluar dari sesi akun (Logout) | Multi-Role | Frontend Playwright | ✅ **PASSED** |
| **TC009** | Menyimpan dokumen kerja sama baru sebagai draft (UC08) | Jurusan / Unit | Frontend / Backend | ✅ **PASSED** |
| **TC010** | Mitra mengajukan perpanjangan kerja sama (UC18) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC012** | Admin can send login access to partner without account (UC07) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC013** | Admin can add a new user (UC01) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC014** | Pimpinan menolak pengajuan kerja sama dengan alasan (UC17) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC016** | Admin can register a new partner (UC04) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC017** | Mengedit draft dokumen kerja sama (UC09) | Jurusan / Unit | Frontend / Backend | ✅ **PASSED** |
| **TC018** | Admin can create a new role with permissions (UC02) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC019** | Menampilkan validasi saat pengajuan publik belum lengkap (UC15) | Publik | Frontend Playwright | ✅ **PASSED** |
| **TC020** | Admin can add a new cooperation type (UC03) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC021** | Mitra melihat dokumen dan memberikan review draft (UC13) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC022** | Mitra can view own documents and inspect details (UC14) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC023** | Mencegah pengeditan dokumen yang sudah bukan draft (UC09 Guard) | Jurusan / Unit | Frontend / Backend | ✅ **PASSED** |
| **TC024** | Unit Pengusul menginput kegiatan kerja sama (IA/SPK) (UC19) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC025** | Prodi menginput penempatan mahasiswa & pembimbing (UC20) | Prodi | Frontend / Backend | ✅ **PASSED** |
| **TC026** | Mitra industri memberikan penilaian mahasiswa magang (UC21) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC027** | Pimpinan memonitoring mahasiswa aktif & early warning (UC22) | Pimpinan | Frontend / E2E | ✅ **PASSED** |
| **TC028** | Unit pengusul mengisi formulir evaluasi kerja sama (UC23) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC029** | Unit pengusul mensubmit hasil evaluasi ke pimpinan (UC24) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC030** | Pimpinan memvalidasi & mengesahkan evaluasi (UC25) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC031** | Mitra memberikan umpan balik kepuasan kerja sama CSAT (UC26) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC032** | Pimpinan melihat dashboard eksekutif dan KPI utama (UC27) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC033** | Unit kerja melihat dashboard spesifik per-unit (UC28) | Humas/Jurusan/UPA/Pusat/Prodi | Frontend / Backend | ✅ **PASSED** |
| **TC034** | Mitra industri melihat dashboard kemitraan (UC29) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC035** | Admin dan Pimpinan mengekspor laporan PDF & Excel/CSV (UC30) | Admin / Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC036** | Pengguna melihat analitik status KS, klasifikasi, & geo (UC31) | Multi-Role / Unit | Frontend / Backend | ✅ **PASSED** |

---

## 4️⃣ Kesimpulan
Seluruh alur proses bisnis pada **Subsistem Laporan dan Dashboard (UC27 s.d. UC31)** telah teruji tuntas:
1. Dashboard Eksekutif Pimpinan (**UC27**)
2. Dashboard Spesifik Per-Unit Humas, Jurusan, Prodi, UPA, Pusat (**UC28**)
3. Portal Dashboard Kemitraan Mitra Industri (**UC29**)
4. Ekspor Laporan Komprehensif PDF & Excel/CSV (**UC30**)
5. Visualisasi Analitik Status, Klasifikasi, dan Sebaran Geografis Mitra (**UC31**)

Dengan penambahan ini, total **34 Kasus Uji (TC001–TC036)** pada TestSprite telah berhasil diverifikasi dengan tingkat kelulusan sempurna **100% PASSED**.
