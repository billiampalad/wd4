# TestSprite Testing Report — Subsistem Master Data, Dokumen KS, & Pengajuan Kerja Sama (UC01 s.d. UC18)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** 
  - [analysis-flowchart.md (L51–L145: Subsistem Autentikasi)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L51-L145)
  - [analysis-flowchart.md (L147–L464: Subsistem Master Data UC01–UC07)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L147-L464)
  - [analysis-flowchart.md (L466–L714: Subsistem Dokumen Kerja Sama UC08–UC14)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L466-L714)
  - [analysis-flowchart.md (L716–L863: Subsistem Pengajuan Kerja Sama UC15–UC18)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L716-L863)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-19
- **Prepared by:** TestSprite AI Testing Agent & Antigravity QA Team
- **Akun Pengujian:**
  - **Pimpinan:** `pimpinan@polnado.ac.id` / `password` (Portal: `/login`)
  - **Mitra:** `mitra@industri.co.id` / `password` (Portal: `/login`)
  - **Internal Unit (Humas, Jurusan, UPA, Pusat):** `/login`
  - **Admin:** `admin@polnado.ac.id` / `password` (Portal: `/admin/login`)
- **Overall Status:**
  - **Backend Test Suite (UC15–UC18):** ✅ **100% Passed (8/8 Scenarios Passed)**
  - **Frontend E2E Suite (Playwright):** ✅ **100% Passed (Wizard Pengajuan Publik, Portal Mitra, Antrean & Validasi Pimpinan)**
  - **Total Test Cases in TestSprite:** **21 Test Cases (100% PASSED)**
  - **Critical Bug Fixed:** Resolved SQL Column Error `roles.name` → `roles.role_name` in [PengajuanKerjasamaMitraController.php](file:///c:/laragon/www/wd4/app/Http/Controllers/Pimpinan/PengajuanKerjasamaMitraController.php).

---

## 2️⃣ Hasil Pengujian Subsistem Pengajuan Kerja Sama (UC15 s.d. UC18)

### 📋 5.1 UC15 — Mengajukan Kerja Sama Baru
- **Aktor:** Mitra (Publik & Terautentikasi)
- **Test ID / Code:** `TC001`, `TC002`, `TC003`, `TC019` / `test_backend_uc15_uc18.php` & `verify_e2e_uc15_uc18.py`
- **Status:** ✅ **PASSED**
- **Skenario & Alur Pengujian:**
  1. **Alur Publik (Landing Page `/pengajuan-kerjasama`):**
     - Mitra baru mengakses wizard 5 langkah (1: Identitas Lembaga, 2: Kontak Lembaga, 3: Rencana Kerja Sama, 4: Tinjau Rangkuman, 5: Deklarasi & Kirim).
     - Mengisi nama instansi, klasifikasi, kategori nasional/internasional, negara, kontak, nama & jabatan penandatangan, email, judul pengajuan, tujuan, ruang lingkup usulan.
     - Melakukan submit form publik.
  2. **Validasi Guard Email Terdaftar:**
     - Jika form publik diisi dengan email yang sudah memiliki akun di sistem (`mitra@industri.co.id`), sistem memblokir dan menampilkan peringatan agar login terlebih dahulu sesuai diagram alir UC15.
  3. **Alur Portal Mitra Terautentikasi (`/mitra/pengajuan/create`):**
     - Mitra login ke portal, data identitas lembaga dan kontak terisi secara otomatis (*pre-filled*).
     - Mitra mengisi detail ruang lingkup tambahan dan mengirimkan usulan kerja sama.
- **Hasil & Verifikasi:**
  - Data pengajuan tersimpan di tabel `pengajuan_kerjasama_baru` dengan status awal `status = 'diajukan'` (*Menunggu*).
  - Notifikasi otomatis terkirim ke seluruh akun Pimpinan dan tercatat pada tabel `notifikasis`.

---

### 📋 5.2 UC16 — Menerima Pengajuan Kerja Sama Baru
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC005` / `test_backend_uc15_uc18.php` & `verify_e2e_uc15_uc18.py`
- **Status:** ✅ **PASSED**
- **Skenario & Alur Pengujian:**
  1. Pimpinan login dengan akun `pimpinan@polnado.ac.id`.
  2. Menerima notifikasi pengajuan baru dan membuka menu antrean pengajuan masuk di `/pimpinan/pengajuan-mitra`.
  3. Sistem menampilkan daftar pengajuan berstatus `diajukan` (*Menunggu Review*).
  4. Pimpinan membuka detail pengajuan melalui modal review untuk memeriksa profil mitra, ruang lingkup, contact person, dan proposal.
- **Hasil & Verifikasi:**
  - Antrean validasi memuat seluruh pengajuan baru secara akurat dan real-time.
  - Modal review menampilkan detail profil instansi dan rincian usulan kerja sama secara utuh.

---

### 📋 5.3 UC17 — Memvalidasi Pengajuan Kerja Sama Baru
- **Aktor:** Pimpinan
- **Test ID / Code:** `TC005` & `TC014` / `test_backend_uc15_uc18.php` & `PengajuanKerjasamaMitraController.php`
- **Status:** ✅ **PASSED (Bug Fixed)**
- **Skenario & Alur Pengujian:**
  1. **Keputusan Setujui (Approval):**
     - Pimpinan mengklik tombol *Setujui*, mencantumkan catatan pimpinan.
     - Sistem mengubah status pengajuan menjadi `disetujui`.
     - Data mitra otomatis dicatat/diperbarui pada tabel master `mitras`.
     - **Auto-create Akun Mitra:** Jika mitra belum memiliki akun login, sistem otomatis meng-generate akun User baru dengan role `mitra`, membuat password acak, dan menghubungkan `mitra_id`.
     - Notifikasi dan email persetujuan otomatis terkirim ke mitra.
  2. **Keputusan Tolak (Rejection):**
     - Pimpinan mengklik tombol *Tolak*, mencantumkan alasan penolakan pada field catatan.
     - Sistem mengubah status pengajuan menjadi `ditolak`.
     - Notifikasi penolakan beserta alasan resmi diteruskan ke mitra.
- **Perbaikan Bug (Bug Fix):**
  - **Temuan:** Controller mengeksekusi raw query `Role::whereRaw('LOWER(TRIM(name)) = ?', ['mitra'])` yang menghasilkan error SQL `Unknown column 'name' in where clause` karena tabel roles menggunakan nama kolom `role_name`.
  - **Perbaikan:** Diperbaiki menjadi `Role::whereRaw('LOWER(TRIM(role_name)) = ?', ['mitra'])` pada fungsi `approveKerjasamaBaru` dan `approvePerpanjangan`.
  - **Hasil:** Alur persetujuan dan pembuatan akun otomatis berjalan 100% mulus tanpa error.

---

### 📋 5.4 UC18 — Mengajukan Perpanjangan Kerja Sama
- **Aktor:** Mitra
- **Test ID / Code:** `TC010` / `test_backend_uc15_uc18.php` & `verify_e2e_uc15_uc18.py`
- **Status:** ✅ **PASSED**
- **Skenario & Alur Pengujian:**
  1. Mitra login ke portal mitra dan mengakses menu pengajuan perpanjangan (`/mitra/perpanjangan/create`).
  2. Sistem menampilkan data kerja sama yang ada dengan field instansi dan nomor dokumen lama terisi secara otomatis (*pre-filled*).
  3. Mitra melengkapi jangka waktu baru (`start_date`, `end_date`), mengunggah surat/dokumen permohonan perpanjangan, dan mencantumkan catatan perpanjangan.
  4. Form disubmit (`POST /perpanjangan-kerjasama`).
  5. Pengajuan tersimpan di tabel `pengajuan_perpanjangan_kerjasama` dengan status `diajukan` dan referensi ke mitra lama.
  6. Notifikasi otomatis masuk ke Pimpinan untuk validasi perpanjangan.
  7. Pimpinan dapat meninjau dan menyetujui perpanjangan kerja sama.
- **Hasil & Verifikasi:**
  - Alur perpanjangan sukses terkirim dari sisi mitra dan dapat diproses oleh Pimpinan dengan notifikasi timbal balik ke Humas/Unit Kerja.

---

## 3️⃣ Matriks Lengkap Status Pengujian di TestSprite (21 Test Cases)

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

---

## 4️⃣ Kesimpulan
Seluruh alur proses bisnis pada **Subsistem Pengajuan Kerja Sama (UC15 s.d. UC18)** sesuai spesifikasi flowchart:
1. Pengajuan Baru Publik & Portal Mitra (**UC15**)
2. Penerimaan & Peninjauan Pengajuan oleh Pimpinan (**UC16**)
3. Validasi, Persetujuan, Pembuatan Akun Otomatis, & Penolakan (**UC17**)
4. Pengajuan Perpanjangan Kerja Sama oleh Mitra (**UC18**)

Telah teruji secara tuntas di tingkat **Backend** dan **Frontend E2E** dengan hasil **100% PASSED** (21 Test Cases terdaftar aktif di TestSprite).
