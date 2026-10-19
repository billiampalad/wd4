# TestSprite Testing Report — Subsistem Master Data, Dokumen KS, Pengajuan, Kegiatan, Monitoring & Evaluasi (UC01 s.d. UC26)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** 
  - [analysis-flowchart.md (L51–L145: Subsistem Autentikasi)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L51-L145)
  - [analysis-flowchart.md (L147–L464: Subsistem Master Data UC01–UC07)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L147-L464)
  - [analysis-flowchart.md (L466–L714: Subsistem Dokumen Kerja Sama UC08–UC14)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L466-L714)
  - [analysis-flowchart.md (L716–L863: Subsistem Pengajuan Kerja Sama UC15–UC18)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L716-L863)
  - [analysis-flowchart.md (L865–L1127: Subsistem Kegiatan, Monitoring & Evaluasi UC19–UC26)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L865-L1127)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-19
- **Prepared by:** TestSprite AI Testing Agent & Antigravity QA Team
- **Akun Pengujian:**
  - **Pimpinan:** `pimpinan@polnado.ac.id` / `password` (Portal: `/login`)
  - **Humas:** `humas@polnado.ac.id` / `password` (Portal: `/login`)
  - **Jurusan:** `jurusan@polnado.ac.id` / `password` (Portal: `/login`)
  - **UPA:** `upa@polnado.ac.id` / `password` (Portal: `/login`)
  - **Pusat:** `pusat@polnado.ac.id` / `password` (Portal: `/login`)
  - **Mitra:** `mitra@industri.co.id` / `password` (Portal: `/login`)
  - **Prodi:** `prodi@polnado.ac.id` / `password` (Portal: `/login`)
  - **Admin:** `admin@polnado.ac.id` / `password` (Portal: `/admin/login`)
- **Overall Status:**
  - **Backend Test Suite (UC19–UC26):** ✅ **100% Passed (8/8 Scenarios Passed)**
  - **Frontend E2E Suite (Playwright UC19–UC26):** ✅ **100% Passed (8/8 Scenarios Passed)**
  - **Total Test Cases in TestSprite:** **29 Test Cases (100% PASSED)**

---

## 2️⃣ Hasil Pengujian Subsistem Kegiatan dan Monitoring (UC19 s.d. UC22)

### 📋 6.1 UC19 — Menginput Kegiatan Kerja Sama
- **Aktor:** Humas, Jurusan, Prodi, UPA, Pusat
- **Test ID / Code:** `TC024` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /unit/kegiatan`, `GET /unit/kegiatan/create`, `POST /unit/kegiatan` (`KegiatanKerjasamaController@store`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Unit pengusul mengakses daftar dan form input kegiatan IA/SPK.
  2. Sistem hanya menampilkan dokumen IA/SPK yang telah berstatus **Disahkan**.
  3. Menginput nama kegiatan, jenis kerjasama, tanggal pelaksanaan, estimasi mahasiswa/dosen terlibat.
  4. Kegiatan berhasil tersimpan dan terhubung langsung ke ID Dokumen Kerja Sama terkait.

---

### 📋 6.2 UC20 — Menginput Peserta Mahasiswa Kegiatan
- **Aktor:** Program Studi (Prodi)
- **Test ID / Code:** `TC025` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /prodi/penempatan`, `GET /prodi/penempatan/create`, `POST /prodi/penempatan` (`PenempatanMahasiswaController@store`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Prodi login dan membuka form penempatan mahasiswa magang.
  2. Memilih mahasiswa, kegiatan kerja sama, dan mitra DUDIKA.
  3. Menetapkan masa berlaku magang (`periode_mulai`, `periode_selesai`).
  4. Menetapkan 2 pembimbing:
     - **Pembimbing Internal:** Dosen Polimdo beserta kontak WhatsApp.
     - **Pembimbing Eksternal:** Pembimbing lapangan industri beserta email/telepon.
  5. Dilengkapi validasi pencegahan duplikasi (*guard duplicate placement*). Data berhasil tersimpan dengan status `Aktif`.

---

### 📋 6.3 UC21 — Memberi Penilaian Mahasiswa
- **Aktor:** Mitra Industri
- **Test ID / Code:** `TC026` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /mitra/penilaian`, `PUT /mitra/penilaian/{id}` (`PenilaianMahasiswaController@update`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Mitra login ke portal mitra dan membuka menu *Kegiatan & Magang*.
  2. Menampilkan KPI card peserta magang aktif dan antrean penilaian.
  3. Mitra menginput nilai angka (0–100) dan catatan evaluasi kinerja mahasiswa.
  4. Status penempatan otomatis beralih ke `Selesai` dan notifikasi instan terkirim ke Program Studi dan Jurusan terkait.

---

### 📋 6.4 UC22 — Memonitoring Mahasiswa Aktif
- **Aktor:** Pimpinan, Jurusan, Prodi, Mitra
- **Test ID / Code:** `TC027` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /pimpinan/monitoring`, `GET /prodi/mahasiswa-magang`
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Menampilkan Executive Banner & Early Warning System: masa berlaku, dokumen kritis, dan sebaran unit.
  2. Menampilkan metrik monitoring mahasiswa magang aktif: Total Mahasiswa, Status Selesai, Jumlah Dinilai, dan Rata-rata Nilai Magang (90.5/100).
  3. Visualisasi data responsif dengan filter tahun dan unit kerja.

---

## 3️⃣ Hasil Pengujian Subsistem Evaluasi Kerja Sama (UC23 s.d. UC26)

### 📋 7.1 UC23 — Mengisi Form Evaluasi Kerja Sama
- **Aktor:** Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC028` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /{role}/evaluasi`, `POST /{role}/evaluasi/{id}` (`{Role}PageController@storeEvaluasi`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Unit pengusul mengakses form evaluasi dokumen kerja sama.
  2. Menilai 5 indikator kinerja (Skala Likert 1–5):
     - Kesesuaian Rencana Pelaksanaan
     - Kualitas Hasil / Output
     - Keterlibatan Mitra
     - Efisiensi Waktu & Sumber Daya
     - Kepuasan Pelaksanaan
  3. Mengisi ringkasan evaluasi, kendala lapangan, rekomendasi keberlanjutan.
  4. Tersimpan sebagai draf evaluasi (`status_validasi = 'Draft'`).

---

### 📋 7.2 UC24 — Mensubmit Evaluasi ke Pimpinan
- **Aktor:** Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC029` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `POST /{role}/evaluasi/{id}/submit` (`{Role}PageController@submitEvaluasiToPimpinan`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Pengusul mengklik tombol *Submit ke Pimpinan* pada dokumen evaluasi yang telah lengkap.
  2. Status dokumen kerja sama berubah menjadi `Menunggu Evaluasi` dan evaluasi berstatus `Menunggu Validasi`.
  3. Notifikasi validasi terkirim secara otomatis ke seluruh akun Pimpinan.

---

### 📋 7.3 UC25 — Memvalidasi Evaluasi
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC030` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /pimpinan/evaluasi`, `POST /pimpinan/evaluate/{id}` (`EvaluasiPimpinanController@evaluate`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Pimpinan membuka portal evaluasi di `/pimpinan/evaluasi` dengan antrean tab unit (Humas, Jurusan, UPA, Pusat).
  2. Memeriksa detail skor evaluasi, kendala, dan rekomendasi unit pengusul.
  3. Pimpinan memberikan keputusan validasi:
     - **Disahkan/Divalidasi:** Evaluasi disetujui, status dokumen beralih ke `Disahkan` / `Aktif`.
     - **Perlu Revisi:** Catatan revisi dikirim kembali ke unit pengusul.
  4. Notifikasi status hasil validasi otomatis diteruskan ke unit kerja pengusul.

---

### 📋 7.4 UC26 — Memberi Umpan Balik Kerja Sama (Mitra)
- **Aktor:** Mitra Industri
- **Test ID / Code:** `TC031` / `test_backend_uc19_uc26.php` & `verify_e2e_uc19_uc26.py`
- **Route & Controller:** `GET /umpan-balik`, `POST /umpan-balik` (`UmpanBalikController@store`)
- **Status:** ✅ **PASSED**
- **Alur & Verifikasi:**
  1. Mitra membuka portal Umpan Balik & Evaluasi Kemitraan (CSAT) di `/umpan-balik`.
  2. Menampilkan KPI card skor kepuasan mitra (CSAT), tingkat kepuasan (%), dan antrean feedback.
  3. Membuka modal *Beri Umpan Balik Baru* dan mengisi kuesioner kepuasan, kesimpulan ('Sangat Baik'), dan saran pengembangan kerja sama.
  4. Data umpan balik tersimpan dengan `tipe_evaluasi = 'Umpan_Balik_Mitra'` dan notifikasi otomatis terkirim ke pimpinan dan unit terkait.

---

## 4️⃣ Matriks Lengkap Status Pengujian di TestSprite (29 Test Cases)

| Test ID | Skenario Pengujian | Aktor | Tipe | Status |
|---|---|---|---|---|
| **TC001** | Mitra mengajukan kerja sama baru melalui portal mitra (UC15) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC002** | Public user can submit a partnership application (UC15) | Publik / Mitra | Frontend Playwright | ✅ **PASSED** |
| **TC003** | Masuk ke dashboard sesuai peran | Multi-Role | Frontend Playwright | ✅ **PASSED** |
| **TC004** | Admin can sign in and reach the dashboard | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC005** | Pimpinan menerima dan memvalidasi pengajuan mitra (UC16, UC17) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC006** | Leader can approve and evaluate documents (UC11, UC12) | Pimpinan | Frontend / E2E | ✅ **PASSED** |
| **TC007** | Mengirim draft dokumen ke pimpinan untuk evaluasi (UC10) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC008** | Keluar dari sesi akun (Logout) | Multi-Role | Frontend Playwright | ✅ **PASSED** |
| **TC009** | Menyimpan dokumen kerja sama baru sebagai draft (UC08) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC010** | Mitra mengajukan perpanjangan kerja sama (UC18) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC012** | Admin can send login access to partner without account (UC07) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC013** | Admin can add a new user (UC01) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC014** | Pimpinan menolak pengajuan kerja sama dengan alasan (UC17) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC016** | Admin can register a new partner (UC04) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC017** | Mengedit draft dokumen kerja sama (UC09) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC018** | Admin can create a new role with permissions (UC02) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC019** | Menampilkan validasi saat pengajuan publik belum lengkap (UC15) | Publik | Frontend Playwright | ✅ **PASSED** |
| **TC020** | Admin can add a new cooperation type (UC03) | Admin | Frontend Playwright | ✅ **PASSED** |
| **TC021** | Mitra melihat dokumen dan memberikan review draft (UC13) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC022** | Mitra can view own documents and inspect details (UC14) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC023** | Mencegah pengeditan dokumen yang sudah bukan draft (UC09 Guard) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC024** | Unit Pengusul menginput kegiatan kerja sama (IA/SPK) (UC19) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC025** | Prodi menginput penempatan mahasiswa & pembimbing (UC20) | Prodi | Frontend / Backend | ✅ **PASSED** |
| **TC026** | Mitra industri memberikan penilaian mahasiswa magang (UC21) | Mitra | Frontend / Backend | ✅ **PASSED** |
| **TC027** | Pimpinan memonitoring mahasiswa aktif & early warning (UC22) | Pimpinan | Frontend / E2E | ✅ **PASSED** |
| **TC028** | Unit pengusul mengisi formulir evaluasi kerja sama (UC23) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC029** | Unit pengusul mensubmit hasil evaluasi ke pimpinan (UC24) | Humas/Jurusan/Unit | Frontend / Backend | ✅ **PASSED** |
| **TC030** | Pimpinan memvalidasi & mengesahkan evaluasi (UC25) | Pimpinan | Frontend / Backend | ✅ **PASSED** |
| **TC031** | Mitra memberikan umpan balik kepuasan kerja sama CSAT (UC26) | Mitra | Frontend / Backend | ✅ **PASSED** |

---

## 5️⃣ Kesimpulan
Seluruh alur proses bisnis pada **Subsistem Kegiatan dan Monitoring (UC19 s.d. UC22)** dan **Subsistem Evaluasi Kerja Sama (UC23 s.d. UC26)** telah sukses diverifikasi:
1. Input Kegiatan berbasis IA/SPK Sah (**UC19**)
2. Penempatan Mahasiswa & Penetapan Pembimbing Internal/Eksternal (**UC20**)
3. Penilaian Kinerja Magang oleh Mitra (**UC21**)
4. Monitoring Real-Time & Early Warning System (**UC22**)
5. Pengisian Form Evaluasi 5 Indikator Kinerja (**UC23**)
6. Pengajuan & Submit Evaluasi ke Pimpinan (**UC24**)
7. Validasi & Pengesahan Hasil Evaluasi (**UC25**)
8. Pengisian Umpan Balik Kepuasan (CSAT) oleh Mitra (**UC26**)

Total **29 Test Cases** terdaftar aktif di TestSprite dengan tingkat kelulusan **100% PASSED**.
