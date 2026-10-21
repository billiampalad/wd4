# TestSprite Testing Report — Subsistem Master Data, Dokumen KS, Pengajuan, Kegiatan, Monitoring, Evaluasi, Laporan & Tracking Lulusan (UC01 s.d. UC33)

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
  - [analysis-flowchart.md (L1271–L1346: Subsistem Tracking Lulusan UC32–UC33)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L1271-L1346)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-21
- **Prepared by:** TestSprite AI Testing Agent & Antigravity QA Team
- **Akun Pengujian:**
  - **Pimpinan:** `pimpinan@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Humas:** `humas@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Jurusan:** `jurusan@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Mitra:** `mitra@industri.co.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Prodi:** `prodi@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/login`)
  - **Admin:** `admin@polnado.ac.id` / `password` (Portal: `http://127.0.0.1:8000/admin/login`)
- **Overall Status:**
  - **Backend Test Suite (UC32–UC33):** ✅ **100% Passed (2/2 Scenarios Passed)**
  - **Frontend E2E Suite (Playwright UC32–UC33):** ✅ **100% Passed (2/2 Scenarios Passed)**
  - **Total Test Cases in TestSprite:** **36 Test Cases (100% PASSED)**

---

## 2️⃣ Hasil Pengujian Subsistem Tracking Lulusan (UC32 s.d. UC33)

### 📋 9.1 UC32 — Menginput Data Lulusan Bekerja di Mitra
- **Aktor:** Program Studi (Prodi) & Mitra Industri
- **Test ID / Code:** `TC037` / `test_backend_uc32_uc33.php` & `verify_e2e_uc32_uc33.py`
- **Route & Controller:**
  - **Prodi:** `GET /prodi/alumni`, `GET /prodi/alumni/create`, `POST /prodi/alumni` (`Prodi\AlumniMitraController@store`)
  - **Mitra:** `GET /alumni`, `POST /alumni`, `PUT /alumni/{id}` (`Mitra\AlumniMitraController@store`, `@update`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. **Aktor Prodi:**
     - Mengisi data alumni (NIM, Nama, Program Studi, Tahun Kelulusan, Kontak).
     - Menghubungkan alumni ke perusahaan/instansi mitra tempat bekerja, menetapkan posisi/jabatan, tahun mulai bekerja, dan status aktif.
     - Terverifikasi tersimpan di tabel `alumnis` dan relasi `alumni_mitras` dengan flag `sumber_data = 'Prodi'`.
  2. **Aktor Mitra Industri:**
     - Mitra dapat mencatat alumni yang bekerja di perusahaannya melalui portal mitra `/alumni`.
     - Menyediakan mode autocomplete master alumni terdaftar maupun pendaftaran alumni baru.
     - Menyediakan fitur update posisi/jabatan dan status karir alumni (`PUT /alumni/{id}`).
     - Dilengkapi proteksi validasi pencegahan duplikasi alumni pada mitra yang sama.

---

### 📋 9.2 UC33 — Melihat Statistik Penyerapan Lulusan
- **Aktor:** Pimpinan, Humas, Jurusan, Prodi, Mitra
- **Test ID / Code:** `TC038` / `test_backend_uc32_uc33.php` & `verify_e2e_uc32_uc33.py`
- **Route & Controller:**
  - `GET /pimpinan` (`DashboardController@pimpinan`)
  - `GET /prodi` (`DashboardController@prodi`)
  - `GET /alumni` (`Mitra\AlumniMitraController@index`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. **Executive Dashboard Pimpinan (`/pimpinan`):**
     - Menampilkan indikator kinerja utama IKU 1: Total Alumni Terdaftar (11), Alumni Bekerja di Mitra (8), dan Rasio Penyerapan Lulusan (72.73%).
  2. **Dashboard Prodi (`/prodi`):**
     - Menampilkan statistik penyerapan alumni prodi (Total: 7, Bekerja: 6, Persentase Penyerapan: 85.71%), status distribusi, tren tahunan, dan tabel 5 alumni bekerja terkini.
  3. **Portal Mitra (`/alumni`):**
     - Menampilkan KPI Card instansi mitra: Total Alumni Bekerja (9), Status Aktif (7), Resign/Selesai, dan Sebaran Program Studi yang terserap (4 Prodi).
     - Menyediakan filter interaktif berdasarkan Program Studi dan Tahun Kelulusan.

---

## 3️⃣ Matriks Lengkap Kasus Uji TestSprite (36 Test Cases)

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
| **TC037** | Menginput data lulusan bekerja di mitra (Prodi & Mitra) (UC32) | Prodi / Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC038** | Melihat statistik penyerapan lulusan IKU 1 (UC33) | Pimpinan/Prodi/Mitra | Frontend / Backend | ✅ **PASSED** |

---

## 4️⃣ Kesimpulan
Seluruh alur proses bisnis pada **Subsistem Tracking Lulusan (UC32 s.d. UC33)** telah diverifikasi secara tuntas:
1. Input Data Lulusan Bekerja oleh Prodi & Mitra (**UC32**)
2. Monitoring & Visualisasi Statistik Penyerapan Lulusan IKU 1 (**UC33**)

Total **36 Kasus Uji (TC001–TC038)** kini terdaftar aktif di TestSprite dengan kelulusan sempurna **100% PASSED**.
