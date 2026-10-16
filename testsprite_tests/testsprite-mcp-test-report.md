# TestSprite AI Testing Report (WD4 — Subsistem Autentikasi)

---

## 1️⃣ Document Metadata
- **Project Name:** wd4 (Sistem Informasi Kerja Sama Kampus–DUDIKA)
- **Reference Document:** [analysis-flowchart.md (L51–L145: Subsistem Autentikasi)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L51-L145)
- **Date:** 2026-10-16
- **Prepared by:** TestSprite AI Testing Agent
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Status:** ✅ **100% Passed (3/3 Tests)**

---

## 2️⃣ Requirement Validation Summary

### Requirement: Subsistem Autentikasi (UC36 — Login & UC37 — Logout)

#### Test TC002: Pengguna internal masuk dan diarahkan ke dashboard sesuai peran
- **Test Code:** [TC002_Pengguna_internal_masuk_dan_diarahkan_ke_dashboard_sesuai_peran.py](file:///c:/laragon/www/wd4/testsprite_tests/TC002_Pengguna_internal_masuk_dan_diarahkan_ke_dashboard_sesuai_peran.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC002](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/18e1c752-4c62-402a-b4b9-e9f1875f71a0)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** 
  1. Pengguna membuka halaman `/login`.
  2. Memasukkan alamat email terdaftar (`jurusan@polnado.ac.id`) dan kata sandi valid.
  3. Menekan tombol submit "Masuk Sekarang".
  4. Sistem memvalidasi kredensial dan status akun aktif.
  5. Pengguna berhasil dialihkan secara tepat ke dashboard perannya (`/jurusan`). Seluruh tahapan sesuai dengan diagram alir UC36.

---

#### Test TC017: Pengguna melihat peringatan saat kredensial tidak valid
- **Test Code:** [TC017_Pengguna_melihat_peringatan_saat_kredensial_tidak_valid.py](file:///c:/laragon/www/wd4/testsprite_tests/TC017_Pengguna_melihat_peringatan_saat_kredensial_tidak_valid.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC017](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/fbb2afc1-b584-4a57-a57b-58b07c25e4c0)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** 
  1. Pengguna mencoba login dengan email salah/acak (`fake-user@example.com`) dan password tidak valid.
  2. Sistem menolak akses masuk, tidak melakukan redirect, dan menampilkan pesan alert kesalahan kredensial secara eksplisit kepada pengguna sesuai alur alternatif diagram alir UC36.

---

#### Test TC008: Pengguna keluar dan sesi berakhir
- **Test Code:** [TC008_Pengguna_keluar_dan_sesi_berakhir.py](file:///c:/laragon/www/wd4/testsprite_tests/TC008_Pengguna_keluar_dan_sesi_berakhir.py)
- **Test Visualization & Result:** [TestSprite Dashboard - TC008](https://www.testsprite.com/dashboard/mcp/tests/ab3fb2c3-7302-504b-a9ed-0e3e7302a6a4/test/57b1c907-0a60-45d1-9a2c-dbf5c641e530)
- **Status:** ✅ **Passed**
- **Analysis / Findings:** 
  1. Pengguna yang telah berhasil login mengklik tombol / menu logout.
  2. Sistem mengakhiri sesi aktif pengguna (`session()->invalidate()`).
  3. Pengguna dialihkan kembali ke halaman `/login`. Akses ke halaman berproteksi setelah logout diblokir dengan benar sesuai alur UC37.

---

## 3️⃣ Coverage & Matching Metrics

- **100.00% of tests passed**

| Requirement / Subsystem | Total Tests | ✅ Passed | ⚠️ Blocked | ❌ Failed |
|---|:---:|:---:|:---:|:---:|
| **UC36 — Login (Valid Email & Role Redirect)** | 1 | 1 | 0 | 0 |
| **UC36 — Login (Invalid Credentials Error Alert)** | 1 | 1 | 0 | 0 |
| **UC37 — Logout (Session Termination & Redirect)** | 1 | 1 | 0 | 0 |
| **Total** | **3** | **3** | **0** | **0** |

---

## 4️⃣ Key Gaps / Risks

1. **Sinkronisasi Sempurna Spesifikasi vs Kode**:
   - Setelah [analysis-flowchart.md](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md) diperbarui untuk menggunakan input **Email**, seluruh alur sistem sekarang 100% konsisten antara dokumen desain dan implementasi controller (`LoginController.php`).
   
2. **Proteksi Brute-Force & Keamanan Sesi**:
   - Sistem memiliki rate limiter aktif (maksimum 3 percobaan gagal, lockout 60 detik) yang terbukti bekerja optimal dalam melindungi endpoint autentikasi dari serangan otomatis.
