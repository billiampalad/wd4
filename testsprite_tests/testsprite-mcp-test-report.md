# TestSprite AI Testing Report (WD4 Sistem Kerjasama)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** [analysis-flowchart.md](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md)
- **Date:** 2026-10-16
- **Prepared by:** TestSprite AI Testing Agent
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)

---

## 2️⃣ Requirement Validation Summary

### Requirement Group 1: Subsistem Autentikasi (UC36 & UC37)

#### Test TC017: Pengguna melihat peringatan saat kredensial tidak valid
- **Test Code:** [TC017_Pengguna_melihat_peringatan_saat_kredensial_tidak_valid.py](file:///c:/laragon/www/wd4/testsprite_tests/TC017_Pengguna_melihat_peringatan_saat_kredensial_tidak_valid.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC017](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/fbb2afc1-b584-4a57-a57b-58b07c25e4c0)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** Ketika pengguna memasukkan email/kredensial acak yang salah (`fake-user@example.com` / `invalid-password`), sistem secara tepat menampilkan pesan peringatan kredensial salah, mencegah akses tidak sah, dan tidak melakukan redirect.

#### Test TC002: Pengguna internal masuk dan diarahkan ke dashboard sesuai peran
- **Test Code:** [TC002_Pengguna_internal_masuk_dan_diarahkan_ke_dashboard_sesuai_peran.py](file:///c:/laragon/www/wd4/testsprite_tests/TC002_Pengguna_internal_masuk_dan_diarahkan_ke_dashboard_sesuai_peran.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC002](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/18e1c752-4c62-402a-b4b9-e9f1875f71a0)
- **Status:** ⚠️ **BLOCKED**
- **Analysis / Findings:** Login form di `resources/views/auth/login.blade.php` dan `LoginController.php` mengharuskan input bertipe `email` (`'email' => 'required|email'`). Ketika pengujian memasukkan NIK murni (seperti `222222`), sistem memvalidasi sebagai kegagalan login dan memicu rate-limiting ("Email atau kata sandi salah. Silakan coba lagi dalam 60 detik."). Akibatnya, pengalihan ke dashboard role tidak tercapai.

#### Test TC008: Pengguna keluar dan sesi berakhir
- **Test Code:** [TC008_Pengguna_keluar_dan_sesi_berakhir.py](file:///c:/laragon/www/wd4/testsprite_tests/TC008_Pengguna_keluar_dan_sesi_berakhir.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC008](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/57b1c907-0a60-45d1-9a2c-dbf5c641e530)
- **Status:** ⚠️ **BLOCKED**
- **Analysis / Findings:** Pengujian logout terblokir karena bergantung pada sesi login aktif yang sebelumnya terhambat oleh rate-limiting dan format kredensial.

---

### Requirement Group 2: Subsistem Pengajuan Kerja Sama Publik (UC15)

#### Test TC003: Mitra baru mengajukan kerja sama publik
- **Test Code:** [TC003_Mitra_baru_mengajukan_kerja_sama_publik.py](file:///c:/laragon/www/wd4/testsprite_tests/TC003_Mitra_baru_mengajukan_kerja_sama_publik.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC003](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/1a0f3f47-6a42-4e68-afce-03557b5770e0)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** Pengujian membuka formulir publik `/pengajuan-kerjasama`, mengisi kelengkapan profil mitra (nama instansi, email, kontak, alamat, bidang kerjasama, ruang lingkup usulan), mengunggah file proposal PDF, dan melakukan submit. Sistem berhasil memproses penyimpanan dan menampilkan halaman/pesan konfirmasi keberhasilan sesuai flowchart UC15.

#### Test TC019: Mitra melihat validasi saat data pengajuan publik belum lengkap
- **Test Code:** [TC019_Mitra_melihat_validasi_saat_data_pengajuan_publik_belum_lengkap.py](file:///c:/laragon/www/wd4/testsprite_tests/TC019_Mitra_melihat_validasi_saat_data_pengajuan_publik_belum_lengkap.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC019](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/b98e728d-8814-4403-bf1b-adb9ab0a5601)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** Ketika formulir disubmit tanpa melengkapi data wajib (seperti email dan detail kontak penting), sistem menolak pengiriman dan menampilkan pesan error validasi pada field terkait sesuai spesifikasi alur alternatif UC15.

---

## 3️⃣ Coverage & Matching Metrics

- **Total Tests Executed:** 5
- **Passed:** 3 (60.00%)
- **Blocked:** 2 (40.00%)
- **Failed:** 0 (0.00%)

| Requirement Group | Total Tests | ✅ Passed | ⚠️ Blocked | ❌ Failed |
|-------------------|-------------|-----------|------------|-----------|
| **Autentikasi (UC36, UC37)** | 3 | 1 | 2 | 0 |
| **Pengajuan Publik (UC15)** | 2 | 2 | 0 | 0 |
| **Total** | **5** | **3** | **2** | **0** |

---

## 4️⃣ Key Gaps / Risks

1. **Gap Spesifikasi Input Login (NIK vs Email)**:
   - Pada dokumen desain flowchart [`analysis-flowchart.md`](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md) (bagian 2.1 UC36), input pengguna didefinisikan sebagai **"Memasukkan NIK/Email"**.
   - Pada implementasi kode aktual (`app/Http/Controllers/Auth/LoginController.php:23`), sistem menerapkan validasi ketat `'email' => 'required|email'`.
   - **Rekomendasi:** Perbarui `LoginController` agar mendukung autentikasi dwifungsi (menerima input NIK numerik atau format Email) menggunakan `filter_var($input, FILTER_VALIDATE_EMAIL)` untuk mencocokkan desain spesifikasi sistem.

2. **Dampak Rate-Limiting terhadap Otomasi Pengujian**:
   - `LoginController` membatasi percobaan login hingga 3 kali dengan durasi lockout 60 detik. Saat uji otomatis dijalankan secara beruntun, lockout dapat memblokir eksekusi pengujian berantai.
   - **Rekomendasi:** Tambahkan bypass atau kelonggaran throttling khusus pada *testing environment* (misalnya via konfigurasi `.env` `APP_ENV=testing` atau `local`).
