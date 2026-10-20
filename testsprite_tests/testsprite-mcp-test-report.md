# TestSprite Testing Report — Subsistem Master Data, Dokumen KS, Pengajuan, Kegiatan, Monitoring, Evaluasi, Laporan, Tracking Lulusan & Komunikasi (UC01 s.d. UC35)

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
  - [analysis-flowchart.md (L1348–L1447: Subsistem Komunikasi UC34–UC35 & Otomatisasi Kedaluwarsa)](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-flowchart.md#L1348-L1447)
- **Target Environment:** Local Server (`http://127.0.0.1:8000`)
- **Date:** 2026-10-21
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

---

## 2️⃣ Executive Summary
Pengujian menyeluruh mencakup **38 Test Case (100% Passed)** yang memverifikasi seluruh subsistem aplikasi kerja sama:
1. **Autentikasi & Multi-Role Guard:** Login Admin di `/admin/login` serta login multi-role di `/login`.
2. **Master Data & Mitra (UC01–UC07):** CRUD User, Role, Mitra, dan pengiriman kredensial.
3. **Dokumen Kerja Sama (UC08–UC14):** Alur MoU, MoA, IA, validasi, pengesahan, dan revisi.
4. **Pengajuan Kerja Sama (UC15–UC18):** Pengajuan baru & perpanjangan online.
5. **Kegiatan, Monitoring & Evaluasi (UC19–UC26):** Kegiatan berbasis IA, magang mahasiswa, penilaian mitra, evaluasi berkala, dan CSAT umpan balik.
6. **Laporan & Dashboard Eksekutif (UC27–UC31):** Dashboard Pimpinan, Unit, Mitra, filter multi-dimensi, dan export PDF/Excel.
7. **Tracking Lulusan (UC32–UC33):** Input alumni bekerja di mitra (Prodi & Mitra) dan metrik statistik penyerapan IKU 1.
8. **Subsistem Komunikasi & Notifikasi Terarah (UC34–UC35):** Notifikasi sistem real-time, bell counter badge, otomatisasi pengecekan dokumen kedaluwarsa (`notifikasi:check-expired`), deduplikasi/idempotensi, serta formulir Hubungi Administrator dari portal Mitra.

---

## 3️⃣ Rekapitulasi Kasus Uji (Test Cases Breakdown)

| No | Test ID | Use Case & Fitur | Tipe | Status |
|:--:|:-------:|:-----------------|:----:|:------:|
| 1 | `TC001` | Autentikasi Admin & Multi-Role User Guard | E2E | **PASSED** |
| 2 | `TC002` | Pengelolaan Master Data Mitra (UC01) | E2E | **PASSED** |
| 3 | `TC003` | Pengelolaan Master Data Pengguna & Role (UC02) | E2E | **PASSED** |
| 4 | `TC004` | Pengelolaan Master Data Klasifikasi Mitra (UC03) | E2E | **PASSED** |
| 5 | `TC005` | Pengelolaan Master Data Sasaran & Indikator (UC04) | E2E | **PASSED** |
| 6 | `TC006` | Pengelolaan Master Data Unit, Jurusan & Prodi (UC05) | E2E | **PASSED** |
| 7 | `TC007` | Pengiriman Akses Login Mitra Otomatis (UC07) | E2E | **PASSED** |
| 8 | `TC008` | Menginput Draf Dokumen Kerja Sama (UC08) | E2E | **PASSED** |
| 9 | `TC009` | Meninjau & Memvalidasi Draf Dokumen (UC09) | E2E | **PASSED** |
| 10 | `TC010` | Merevisi Draf Dokumen Kerja Sama (UC10) | E2E | **PASSED** |
| 11 | `TC011` | Mengesahkan Dokumen Kerja Sama (UC11) | E2E | **PASSED** |
| 12 | `TC012` | Mengunggah Dokumen Final Bertanda Tangan (UC12) | E2E | **PASSED** |
| 13 | `TC013` | Mengakses Repositori Dokumen Kerja Sama (UC13) | E2E | **PASSED** |
| 14 | `TC014` | Mengunduh Dokumen Kerja Sama (UC14) | E2E | **PASSED** |
| 15 | `TC015` | Mengajukan Kerja Sama Baru Mitra (UC15) | E2E | **PASSED** |
| 16 | `TC016` | Memverifikasi Pengajuan Kerja Sama Baru (UC16) | E2E | **PASSED** |
| 17 | `TC017` | Mengajukan Perpanjangan Kerja Sama (UC17) | E2E | **PASSED** |
| 18 | `TC018` | Memverifikasi Pengajuan Perpanjangan (UC18) | E2E | **PASSED** |
| 19 | `TC019` | Menginput Data Kegiatan Berbasis IA (UC19) | E2E | **PASSED** |
| 20 | `TC020` | Menginput Data Penempatan Mahasiswa Magang (UC20) | E2E | **PASSED** |
| 21 | `TC021` | Memberikan Penilaian Mahasiswa oleh Mitra (UC21) | E2E | **PASSED** |
| 22 | `TC022` | Memantau Pelaksanaan Kerja Sama (UC22) | E2E | **PASSED** |
| 23 | `TC023` | Menginput Evaluasi Pelaksanaan Berkala (UC23) | E2E | **PASSED** |
| 24 | `TC024` | Meninjau & Memvalidasi Hasil Evaluasi (UC24) | E2E | **PASSED** |
| 25 | `TC025` | Merekap Nilai Mahasiswa Magang (UC25) | E2E | **PASSED** |
| 26 | `TC026` | Memberikan Umpan Balik Kepuasan Mitra / CSAT (UC26) | E2E | **PASSED** |
| 27 | `TC027` | Melihat Dashboard Eksekutif Pimpinan (UC27) | E2E | **PASSED** |
| 28 | `TC028` | Melihat Dashboard Unit Kerja / Pelaksana (UC28) | E2E | **PASSED** |
| 29 | `TC029` | Melihat Dashboard Mitra Kerjasama (UC29) | E2E | **PASSED** |
| 30 | `TC030` | Mengunduh Laporan Kerjasama PDF & Excel (UC30) | E2E | **PASSED** |
| 31 | `TC031` | Menganalisis Statistik dan Tren Kerjasama (UC31) | E2E | **PASSED** |
| 32 | `TC032` | Melihat Profil Detail Mitra Terpadu | E2E | **PASSED** |
| 33 | `TC033` | Mengelola Status Keaktifan Mitra Kerjasama | E2E | **PASSED** |
| 34 | `TC034` | Menilai Kinerja Mitra / Performance Scorecard | E2E | **PASSED** |
| 35 | `TC035` | Filter & Pencarian Direktori Mitra | E2E | **PASSED** |
| 36 | `TC036` | Export Rekapitulasi Data Mitra | E2E | **PASSED** |
| 37 | `TC037` | Menginput Data Lulusan Bekerja di Mitra (UC32) | E2E | **PASSED** |
| 38 | `TC038` | Melihat Statistik Penyerapan Lulusan IKU 1 (UC33) | E2E | **PASSED** |
| 39 | `TC039` | Menerima Notifikasi Sistem & Pengecekan Kedaluwarsa Otomatis (UC34 & Section 10.3) | E2E | **PASSED** |
| 40 | `TC040` | Menghubungi Administrator dari Portal Mitra (UC35) | E2E | **PASSED** |

> **Statistik Hasil Uji Akhir:** 38 Test Case Terverifikasi | **38 Passed (100%)** | 0 Failed | 0 Blocked
