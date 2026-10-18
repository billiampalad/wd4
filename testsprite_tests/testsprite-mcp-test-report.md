# TestSprite Testing Report — Subsistem Master Data & Dokumen Kerja Sama (UC08 s.d. UC14)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** 
  - [analysis-flowchart.md (L51–L145: Subsistem Autentikasi)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L51-L145)
  - [analysis-flowchart.md (L147–L464: Subsistem Master Data UC01–UC07)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L147-L464)
  - [analysis-flowchart.md (L466–L714: Subsistem Dokumen Kerja Sama UC08–UC14)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L466-L714)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-18
- **Prepared by:** TestSprite AI Testing Agent & Antigravity QA Team
- **Test Accounts & Portals:**
  - **Admin:** `admin@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/admin/login`
  - **Pimpinan:** `pimpinan@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/login`
  - **Humas:** `humas@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/login`
  - **Jurusan:** `jurusan@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/login`
  - **UPA:** `upa@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/login`
  - **Pusat:** `pusat@polnado.ac.id` / `password` — Portal: `http://127.0.0.1:8000/login`
  - **Mitra:** `mitra@industri.co.id` / `password` — Portal: `http://127.0.0.1:8000/login`
- **Overall Status:**
  - **Backend Test Suite (UC08–UC14):** ✅ **100% Passed (8/8 Scenarios Passed)**
  - **Frontend E2E Suite (Playwright Browser):** ✅ **100% Passed across All 6 Roles**
  - **Master Data & Auth Previous Suites:** ✅ **100% Passed (TC002, TC003, TC004, TC008, TC012, TC013, TC016, TC017, TC018, TC019, TC020 Passed)**
  - **Total Test Cases in TestSprite:** **17 Test Cases (100% PASSED)**

---

## 2️⃣ Hasil Pengujian Subsistem Dokumen Kerja Sama (UC08 s.d. UC14)

### 📋 4.1 UC08 — Menginput Dokumen Kerja Sama
- **Aktor:** Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC009` / `TC009_Menyimpan_dokumen_kerja_sama_baru_sebagai_draft.py` & `test_backend_uc08_uc14.php`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Pengguna internal unit login melalui portal `http://127.0.0.1:8000/login`.
  2. Membuka menu navigasi dokumen kerja sama (`/{role}/data-kerjasama/create`).
  3. Memilih jenis dokumen (MoU, MoA, atau IA/SPK).
  4. Mengisi formulir dokumen: Judul Kerja Sama, Nomor Dokumen, Mitra Terkait, Tanggal Mulai & Tanggal Berakhir, Ruang Lingkup, serta Pejabat Penandatangan.
  5. Menekan tombol simpan kerja sama.
- **Hasil & Verifikasi:**
  - Dokumen berhasil tersimpan di tabel `cooperations` dengan status awal `status_dokumen = 'Draft'` dan `status_berlaku = 'Proses'`.
  - Dokumen langsung muncul pada daftar repositori kerja sama masing-masing unit dengan badge status Draft.

---

### 📋 4.2 UC09 — Mengedit Dokumen Kerja Sama
- **Aktor:** Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC017` & `TC023` / `test_backend_uc08_uc14.php`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Membuka repositori dokumen kerja sama unit.
  2. Memilih dokumen berstatus `Draft` atau `Revisi` untuk disunting (`/{role}/data-kerjasama/{id}/edit`).
  3. Mengubah atribut judul, rincian luaran kegiatan, dan deskripsi.
  4. Menyimpan perubahan dan memverifikasi data yang ter-update.
  5. Melakukan pengujian proteksi: dokumen yang berstatus selain Draft/Revisi (misalnya `Disahkan` atau `Menunggu Evaluasi`) tidak boleh dapat diedit.
- **Hasil & Verifikasi:**
  - Perubahan data pada draft tersimpan secara presisi di database.
  - Guard controller berhasil memproteksi dokumen yang sudah disahkan/menunggu evaluasi sehingga integritas dokumen tetap terjaga.

---

### 📋 4.3 UC10 — Mensubmit Dokumen ke Pimpinan
- **Aktor:** Humas, Jurusan, UPA, Pusat
- **Test ID / Code:** `TC007` / `TC007_Mengirim_draft_dokumen_ke_pimpinan_untuk_evaluasi.py` & `test_backend_uc08_uc14.php`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Pengguna unit membuka detail dokumen kerja sama yang masih berstatus `Draft` (`/{role}/data-kerjasama/{id}`).
  2. Tombol `Minta Persetujuan Pimpinan` aktif dan tersedia.
  3. Mengklik tombol konfirmasi submit ke Pimpinan (`POST /{role}/data-kerjasama/{id}/submit`).
  4. Memvalidasi perubahan status dokumen menjadi `Menunggu Evaluasi`.
  5. Memvalidasi notifikasi otomatis terkirim ke seluruh akun ber-role `pimpinan`.
- **Hasil & Verifikasi:**
  - Status dokumen berpindah dari `Draft` → `Menunggu Evaluasi`.
  - Notifikasi otomatis tergenerate di tabel `notifikasis` dengan tipe `evaluasi` dan tujuan langsung ke dashboard evaluasi Pimpinan.
  - Tombol edit dan submit langsung terkunci/nonaktif setelah dokumen disubmit.

---

### 📋 4.4 UC11 — Memvalidasi Dokumen Kerja Sama
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC006` / `test_backend_uc08_uc14.php` & `verify_e2e_uc08_uc14.py`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Pimpinan login melalui `http://127.0.0.1:8000/login` dengan akun `pimpinan@polnado.ac.id`.
  2. Membuka menu evaluasi Pimpinan (`/pimpinan/evaluasi`).
  3. Sistem menampilkan daftar dokumen yang membutuhkan validasi dan evaluasi (`status_dokumen = 'Menunggu Evaluasi'`).
  4. Pimpinan membuka detail dokumen (`/pimpinan/evaluasi/{id}`) untuk memeriksa kelengkapan data, file dokumen, ruang lingkup, dan mitra.
- **Hasil & Verifikasi:**
  - Halaman antrean evaluasi menampilkan dokumen yang disubmit oleh unit pengusul secara real-time.
  - Detail dokumen, data mitra, dan riwayat pengajuan termuat lengkap tanpa kendala.

---

### 📋 4.5 UC12 — Mengesahkan Dokumen Kerja Sama
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC006` / `test_backend_uc08_uc14.php` & `verify_e2e_uc08_uc14.py`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Pimpinan mengisi formulir evaluasi kelayakan:
     - Memberikan skor kualitas (1 s.d. 5) pada 5 indikator: Sesuai Rencana, Kualitas, Keterlibatan, Efisiensi, Kepuasan.
     - Mengisi ringkasan evaluasi eksekutif dan rekomendasi.
     - Memilih status keputusan `Layak` untuk disahkan (atau `Revisi` jika memerlukan perbaikan).
  2. Menekan tombol validasi & pengesahan (`POST /pimpinan/evaluate/{id}`).
  3. Memverifikasi pembaruan status dokumen menjadi `Disahkan` dan masa berlaku menjadi `Aktif`.
  4. Memverifikasi notifikasi pengesahan terkirim kembali ke unit pengusul.
- **Hasil & Verifikasi:**
  - Record evaluasi tersimpan di tabel `evaluasis` dengan evaluator ID Pimpinan.
  - Status dokumen berubah menjadi `Disahkan` dan `status_berlaku` menjadi `Aktif`.
  - Notifikasi konfirmasi pengesahan otomatis terdistribusi ke unit pengusul.

---

### 📋 4.6 UC13 — Mereview Draf Dokumen Online
- **Aktor:** Mitra
- **Test ID / Code:** `TC021` / `TC021_Mitra_melihat_dokumen_dan_memberikan_review_pada_draft.py` & `test_backend_uc08_uc14.php`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Mitra login ke Portal Mitra dengan akun `mitra@industri.co.id`.
  2. Membuka detail dokumen kerja sama terkait mitra (`/mitra/dokumen/{id}`).
  3. Membaca draf klausul dan ruang lingkup kerja sama.
  4. Pada panel `Catatan Review Draf (Mitra)`, mitra menuliskan telaah, umpan balik pasal, atau persetujuan.
  5. Menekan tombol `Kirim Catatan Review` (`POST /mitra/dokumen/{id}/review`).
- **Hasil & Verifikasi:**
  - Catatan review draf terkirim dan tersimpan.
  - Notifikasi tipe `review_draf_setuju` / `review_draf` otomatis diteruskan ke unit pengusul Polimdo.

---

### 📋 4.7 UC14 — Melihat Dokumen Kerja Sama Sendiri
- **Aktor:** Mitra
- **Test ID / Code:** `TC022` / `TC022_Mitra_can_view_documents_and_review_a_draft.py` & `test_backend_uc08_uc14.php`
- **Status:** ✅ **PASSED**
- **Skenario Pengujian:**
  1. Mitra membuka halaman repositori dokumen (`/mitra/dokumen`).
  2. Sistem hanya menampilkan dokumen kerja sama yang terikat dengan ID mitra pengguna (`mitra_id = 21` / `PT Industri Mitra`).
  3. Mitra dapat memfilter status dokumen, melihat nomor dokumen, jenis dokumen, masa berlaku, dan berkas lampiran scan PDF.
- **Hasil & Verifikasi:**
  - Data scoping bekerja dengan tepat; mitra hanya melihat dokumen milik organisasinya sendiri tanpa risiko data leakage ke instansi lain.

---

## 3️⃣ Matriks Lengkap Status Pengujian (17 Test Cases)

| Test ID | Skenario Pengujian | Aktor | Tipe | Status |
|---|---|---|---|---|
| **TC002** | Public user can submit a partnership application | Publik / Mitra | Frontend | ✅ **PASSED** |
| **TC003** | Masuk ke dashboard sesuai peran | Multi-Role | Frontend | ✅ **PASSED** |
| **TC004** | Admin can sign in and reach the dashboard | Admin | Frontend | ✅ **PASSED** |
| **TC006** | Leader can approve and evaluate documents (UC11, UC12) | Pimpinan | Frontend / E2E | ✅ **PASSED** |
| **TC007** | Mengirim draft dokumen ke pimpinan untuk evaluasi (UC10) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC008** | Keluar dari sesi akun (Logout) | Multi-Role | Frontend | ✅ **PASSED** |
| **TC009** | Menyimpan dokumen kerja sama baru sebagai draft (UC08) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC012** | Admin can send login access to partner without account | Admin | Frontend | ✅ **PASSED** |
| **TC013** | Admin can add a new user (UC01) | Admin | Frontend | ✅ **PASSED** |
| **TC016** | Admin can register a new partner (UC04) | Admin | Frontend | ✅ **PASSED** |
| **TC017** | Mengedit draft dokumen kerja sama (UC09) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |
| **TC018** | Admin can create a new role with permissions (UC02) | Admin | Frontend | ✅ **PASSED** |
| **TC019** | Menampilkan validasi saat pengajuan publik belum lengkap | Publik | Frontend | ✅ **PASSED** |
| **TC020** | Admin can add a new cooperation type (UC03) | Admin | Frontend | ✅ **PASSED** |
| **TC021** | Mitra melihat dokumen dan memberikan review draft (UC13) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC022** | Mitra can view own documents and inspect details (UC14) | Mitra | Frontend / E2E | ✅ **PASSED** |
| **TC023** | Mencegah pengeditan dokumen yang sudah bukan draft (Guard) | Jurusan / Unit | Frontend / E2E | ✅ **PASSED** |

---

## 4️⃣ Verifikasi Multi-Unit Access (Humas, UPA, Pusat)
Seluruh rute unit internal untuk pengelolaan dokumen kerja sama telah diuji dan terverifikasi 200 OK:
- **Humas (`humas@polnado.ac.id`):** `/unit/data-kerjasama` ✅
- **Jurusan (`jurusan@polnado.ac.id`):** `/jurusan/data-kerjasama` ✅
- **UPA (`upa@polnado.ac.id`):** `/upa/data-kerjasama` ✅
- **Pusat (`pusat@polnado.ac.id`):** `/pusat/data-kerjasama` ✅
- **Mitra (`mitra@industri.co.id`):** `/mitra/dokumen` ✅
- **Pimpinan (`pimpinan@polnado.ac.id`):** `/pimpinan/evaluasi` ✅

---

## 5️⃣ Kesimpulan & Rekomendasi
Semua alur proses bisnis pada **Subsistem Dokumen Kerja Sama (L466 s.d. L714)**:
1. Input Dokumen Baru sebagai Draft (**UC08**)
2. Penyuntingan Draft & Proteksi Status Non-Draft (**UC09**)
3. Pengiriman Dokumen ke Pimpinan (**UC10**)
4. Pemeriksaan dan Validasi Dokumen (**UC11**)
5. Pengesahan Dokumen dan Penilaian Kualitas (**UC12**)
6. Telaah dan Review Online Draf oleh Mitra (**UC13**)
7. Akses Repositori Mandiri Mitra (**UC14**)

Telah teruji secara menyeluruh di tingkat **Backend (Logika, Relasi Database, Transaksi, & Notifikasi)** serta di tingkat **Frontend (Interaksi UI, Form Validation, & E2E Browser Session)** dengan hasil **100% PASSED**.
