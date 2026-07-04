# Product Requirements Document (PRD)
# Pengembangan Alur Lanjutan Pengajuan Kerja Sama Mitra Baru

# 1. Pendahuluan

## 1.1 Latar Belakang

Sistem Pengajuan Kerja Sama Mitra Baru saat ini telah menyediakan proses pengajuan kerja sama oleh Unit Kerja serta proses validasi oleh Pimpinan. Melalui sistem tersebut, pengguna dapat mengajukan kerja sama dan Pimpinan dapat memberikan keputusan terhadap pengajuan yang masuk.

Namun, proses administrasi setelah pengajuan disetujui masih belum terintegrasi di dalam sistem. Setelah memperoleh persetujuan dari Pimpinan, pengelolaan data mitra, pelengkapan dokumen, penugasan kepada Humas, hingga aktivasi kerja sama masih dilakukan secara manual.

Akibatnya, proses menjadi sulit dipantau, status kerja sama tidak terdokumentasi dengan baik, serta berpotensi menimbulkan keterlambatan dalam penyelesaian administrasi.

Oleh karena itu, diperlukan pengembangan modul lanjutan yang mampu mengelola seluruh proses setelah validasi Pimpinan hingga kerja sama resmi berstatus Aktif.

---

# 2. Tujuan

Pengembangan ini bertujuan untuk:

- Mengotomatisasi proses setelah keputusan Pimpinan.
- Mengurangi pekerjaan administrasi secara manual.
- Memudahkan monitoring status kerja sama.
- Mengintegrasikan data pengajuan dengan Master Mitra.
- Memastikan seluruh dokumen kerja sama lengkap sebelum kerja sama diaktifkan.

---

# 3. Ruang Lingkup

## 3.1 Modul Existing (Sudah Berjalan)

Tahapan berikut **sudah tersedia pada sistem** dan **tidak termasuk dalam pengembangan PRD ini**.

```
Pengajuan Kerja Sama Baru
        │
        ▼
Notifikasi kepada Pimpinan
        │
        ▼
Pimpinan Validasi Pengajuan
```

Modul tersebut hanya dijadikan sebagai titik awal integrasi.

---

## 3.2 Modul yang Akan Dikembangkan

Pengembangan dimulai setelah Pimpinan memberikan keputusan terhadap pengajuan.

Tahapan yang akan dibangun meliputi:

- Keputusan Disetujui / Ditolak
- Pengiriman Email Otomatis
- Perubahan Status Pengajuan
- Pembuatan Data Master Mitra
- Distribusi kepada Humas/Unit Kerja
- Pelengkapan Data Mitra
- Validasi Kelengkapan Dokumen
- Aktivasi Kerja Sama

---

# 4. Business Process

## Tahap 1 — Validasi Pimpinan

Tahap ini merupakan titik integrasi dengan sistem yang telah tersedia.

Pimpinan melakukan pemeriksaan terhadap data pengajuan yang telah diajukan sebelumnya.

Setelah proses validasi selesai, sistem menerima salah satu keputusan berikut.

- Disetujui
- Ditolak

---

## Tahap 2 — Pengajuan Ditolak

Apabila pengajuan ditolak oleh Pimpinan maka sistem akan secara otomatis:

- mengubah status menjadi **Ditolak**
- mengirim email penolakan kepada pemohon
- menyimpan pengajuan sebagai arsip

Proses selesai.

---

## Tahap 3 — Pengajuan Disetujui

Apabila pengajuan disetujui maka sistem akan:

- mengubah status menjadi **Disetujui**
- mengirim email persetujuan
- membuat data baru pada Master Mitra

Selanjutnya sistem meneruskan data tersebut kepada Humas atau Unit Kerja yang bertanggung jawab.

---

## Tahap 4 — Proses Administrasi

Setelah data masuk ke Master Mitra, sistem akan:

- mengubah status menjadi **Proses**
- mengirim notifikasi kepada Humas
- menunggu proses pelengkapan data

Pada tahap ini Humas bertugas:

- melengkapi identitas mitra
- melengkapi alamat
- melengkapi PIC
- mengunggah dokumen kerja sama
- melengkapi informasi pendukung lainnya

---

## Tahap 5 — Pemeriksaan Kelengkapan

Sistem melakukan pemeriksaan terhadap data yang telah dilengkapi.

### Jika belum lengkap

Status tetap berada pada **Proses**.

Humas dapat kembali melengkapi data hingga seluruh persyaratan terpenuhi.

### Jika sudah lengkap

Humas dapat mengubah status menjadi **Aktif**.

---

## Tahap 6 — Aktivasi Kerja Sama

Setelah seluruh data dan dokumen dinyatakan lengkap, kerja sama resmi diaktifkan.

Status berubah menjadi:

```
Aktif
```

Proses administrasi selesai.

---

# 5. Alur Sistem

```
(EXISTING)

Pengajuan Kerja Sama
        │
        ▼
Notifikasi ke Pimpinan
        │
        ▼
Pimpinan Validasi
        │
═══════════════════════════════════════

(TO BUILD)

        ▼
      Disahkan?
      │      │
     Tidak   Ya
      │      │
      ▼      ▼
Email      Email
Penolakan Persetujuan
      │      │
      ▼      ▼
Status    Status
Ditolak  Disetujui
      │      │
      ▼      ▼
 Arsip   Master Mitra
              │
              ▼
     Notifikasi Humas
              │
              ▼
        Status Proses
              │
              ▼
      Lengkapi Data
              │
              ▼
      Data Lengkap?
         │      │
      Tidak     Ya
         │      │
         └──────┘
              │
              ▼
      Status Aktif
```

---

# 6. Status Pengajuan

| Status | Deskripsi |
|----------|-----------|
| Baru | Pengajuan telah dibuat dan menunggu validasi Pimpinan |
| Ditolak | Pengajuan tidak disetujui Pimpinan |
| Disetujui | Pengajuan disetujui dan siap diproses lebih lanjut |
| Proses | Data dan dokumen sedang dilengkapi oleh Humas |
| Aktif | Kerja sama telah resmi aktif |

---

# 7. Aktor Sistem

## Pemohon

Hak akses:

- Mengajukan kerja sama
- Melihat status pengajuan

---

## Pimpinan

Hak akses:

- Melihat daftar pengajuan
- Menyetujui pengajuan
- Menolak pengajuan

---

## Humas / Unit Kerja

Hak akses:

- Melihat daftar kerja sama berstatus Proses
- Melengkapi data mitra
- Mengunggah dokumen
- Mengaktifkan kerja sama

---

## Administrator

Hak akses:

- Mengelola seluruh data
- Monitoring
- Audit Log

---

# 8. Functional Requirements

## FR-1 Integrasi Hasil Validasi

Sistem menerima keputusan dari modul validasi Pimpinan yang telah tersedia.

Keputusan hanya terdiri dari:

- Disetujui
- Ditolak

---

## FR-2 Penolakan

Jika pengajuan ditolak maka sistem:

- mengubah status menjadi Ditolak
- mengirim email penolakan
- menyimpan data pada arsip

---

## FR-3 Persetujuan

Jika pengajuan disetujui maka sistem:

- mengubah status menjadi Disetujui
- mengirim email persetujuan
- membuat data Master Mitra

---

## FR-4 Master Mitra

Sistem otomatis:

- membuat data mitra
- menghubungkan data dengan pengajuan
- meneruskan data kepada Humas
- mengubah status menjadi Proses

---

## FR-5 Kelengkapan Data

Humas dapat:

- memperbarui informasi mitra
- mengunggah dokumen
- melihat checklist kelengkapan

Jika data belum lengkap maka status tetap Proses.

---

## FR-6 Aktivasi

Jika seluruh persyaratan terpenuhi maka Humas dapat mengubah status menjadi Aktif.

---

## FR-7 Notifikasi

Sistem mengirim:

- Email Persetujuan
- Email Penolakan
- Notifikasi kepada Humas

Seluruh notifikasi dicatat pada log sistem.

---

# 9. Data Model

## pengajuan_kerjasama

- id
- nomor_pengajuan
- nama_mitra
- status
- tanggal_pengajuan
- tanggal_validasi
- pimpinan_validator
- catatan

---

## master_mitra

- id
- id_pengajuan
- nama_mitra
- status
- pic_humas

---

## dokumen_mitra

- id
- id_mitra
- jenis_dokumen
- file
- status

---

## log_notifikasi

- id
- id_pengajuan
- jenis_notifikasi
- status_pengiriman
- waktu

---

# 10. Acceptance Criteria

## Skenario 1

Pengajuan ditolak.

Hasil yang diharapkan:

- Status Ditolak.
- Email penolakan terkirim.
- Data masuk arsip.

---

## Skenario 2

Pengajuan disetujui.

Hasil yang diharapkan:

- Status Disetujui.
- Email persetujuan terkirim.
- Data otomatis masuk Master Mitra.

---

## Skenario 3

Humas melengkapi data.

Jika data belum lengkap maka status tetap Proses.

---

## Skenario 4

Seluruh data lengkap.

Status berubah menjadi Aktif.

Kerja sama selesai diproses.

---

# 11. Non Functional Requirements

- Seluruh perubahan status tercatat pada Audit Log.
- Perubahan status dilakukan secara real-time.
- Seluruh email memiliki retry apabila gagal dikirim.
- Hak akses menggunakan Role Based Access Control (RBAC).
- Seluruh data terdokumentasi dan dapat ditelusuri.

---

# 12. Roadmap Pengembangan

| Fase | Pengembangan |
|------|--------------|
| Fase 1 | Integrasi hasil validasi Pimpinan |
| Fase 2 | Persetujuan dan Penolakan |
| Fase 3 | Master Mitra |
| Fase 4 | Modul Humas |
| Fase 5 | Kelengkapan Dokumen |
| Fase 6 | Dashboard Monitoring |
| Fase 7 | Testing End-to-End |

# 13. Lampiran

Flowchart:
![Flo   wchart Pengajuan Kerja Sama Mitra Baru](./img/flow-ajukan-kerjasama-mitra-baru.jpg)

---

# 14. Alur Proses Perpanjangan Kerja Sama (Dual-Pathway Integration)

## 14.1 Deskripsi Singkat Fitur & Dual-Pathway Entry Points

Fitur **Perpanjangan Kerja Sama** dirancang dengan dua pintu masuk utama (*Dual-Pathway Entry Points*) yang saling terintegrasi dan saling melengkapi untuk memastikan fleksibilitas alur kerja antara pihak eksternal (Mitra) dan pihak internal (Unit Kerja / Admin Repositori):

1. **Jalur Eksternal / Public Submission (Oleh Mitra / Umum)**:
   - Akses publik tanpa perlu login melalui `/perpanjangan-kerjasama` (Form Wizard 5 Step).
   - Mitra terdaftar memilih dokumen yang akan diperpanjang, memperbarui kontak, menyusun rencana lanjutan (periode mulai–selesai baru, judul, tujuan, ruang lingkup), dan mengunggah **Surat Permohonan Perpanjangan** (format PDF, DOC, atau DOCX max 5MB).
   - Menghasilkan record pengajuan berstatus **`Diajukan`** di tabel `pengajuan_kerjasama_mitras`.
   - Mengirim notifikasi otomatis ke **Pimpinan** untuk proses validasi. Setelah **Disetujui** Pimpinan, sistem secara otomatis memberi notifikasi ke **Unit Kerja** dan memperbarui status dokumen kerja sama terkait di Repositori dari **`Kadaluarsa` / `Aktif`** menjadi **`Dalam Perpanjangan`**.

2. **Jalur Internal / Direct Repository Extension (Oleh Unit Kerja)**:
   - Akses internal berdasar role dari menu **Repositori Kerja Sama** -> Halaman **Detail Data Kerja Sama**.
   - Ketika dokumen kerja sama sudah berstatus **`Kadaluarsa`** (atau H-30 menjelang kadaluarsa), role Unit Kerja / Admin Internal dapat langsung mengeklik tombol **"Perpanjang Kerja Sama"**.
   - Sistem akan langsung mengubah status dokumen di Repositori menjadi **`Dalam Perpanjangan`** dan membuka form draf dokumen perpanjangan baru.

---

## 14.2 Matriks Perubahan Status & Notifikasi (Lifecycle State Machine)

| Status Pengajuan (Publik) | Status Repositori (Internal) | Pemicu (Trigger Event) | Aktor Utama | Dampak & Notifikasi Sistem |
|---------------------------|------------------------------|------------------------|-------------|----------------------------|
| - | **`Kadaluarsa`** | Masa berlaku dokumen habis (`end_date` terlewati) | System Cron / Auto-check | Dokumen ditandai Kadaluarsa. Notifikasi peringatan terkirim ke Unit Kerja & Mitra. |
| **`Diajukan`** | **`Kadaluarsa`** | Mitra mengisi Form Perpanjangan Publik (Step 1-5) | Mitra (Publik) | Record `pengajuan_kerjasama_mitras` dibuat. Notifikasi terkirim ke **Pimpinan**. |
| **`Disetujui`** | **`Dalam Perpanjangan`** | Pimpinan menyetujui pengajuan perpanjangan mitra | Pimpinan | Status pengajuan = `disetujui`. Status dokumen di Repositori otomatis berubah menjadi `Dalam Perpanjangan`. Notifikasi terkirim ke **Unit Kerja**. |
| **`Ditolak`** | **`Kadaluarsa`** | Pimpinan menolak pengajuan perpanjangan mitra | Pimpinan | Status pengajuan = `ditolak`. Status di Repositori tetap `Kadaluarsa`. Email penolakan terkirim ke Mitra. |
| - | **`Dalam Perpanjangan`** | Unit Kerja mengeklik "Perpanjang Kerja Sama" langsung di Repositori | Unit Kerja | Status di Repositori berubah langsung dari `Kadaluarsa` menjadi `Dalam Perpanjangan`. Form draf perpanjangan terbuka. |
| - | **`Aktif`** | Berkas PKS/MoU perpanjangan baru selesai ditandatangan & diunggah | Unit Kerja / Admin | Dokumen perpanjangan baru diterbitkan. Status dokumen baru di Repositori menjadi `Aktif`. |

---

## 14.3 Diagram Flowchart Interkoneksi Perpanjangan (Dual-Pathway Flowchart)

```
                       ┌────────────────────────────────────────────────────────┐
                       │           DOKUMEN KERJA SAMA DI REPOSITORI             │
                       │                 Status: "Kadaluarsa"                    │
                       └───────────────────────────┬────────────────────────────┘
                                                   │
                ┌──────────────────────────────────┴──────────────────────────────────┐
                │                                                                     │
                ▼ (Pintu 1: Pengajuan Publik Mitra)                                   ▼ (Pintu 2: Inisiasi Repositori Internal)
  ┌───────────────────────────┐                                         ┌───────────────────────────┐
  │   Form Wizard Publik 5-Step  │                                         │   Halaman Detail Repositori│
  │   /perpanjangan-kerjasama   │                                         │   (Role: Unit Kerja/Admin)│
  └─────────────┬─────────────┘                                         └─────────────┬─────────────┘
                │                                                                     │
                ▼                                                                     ▼
  ┌───────────────────────────┐                                         ┌───────────────────────────┐
  │ Pilih Mitra & Upload      │                                         │ Klik "Perpanjang          │
  │ Surat Permohonan (.pdf)   │                                         │ Kerja Sama"               │
  └─────────────┬─────────────┘                                         └─────────────┬─────────────┘
                │                                                                     │
                ▼                                                                     │
  ┌───────────────────────────┐                                                       │
  │ Status Pengajuan:         │                                                       │
  │ "Diajukan"                │                                                       │
  └─────────────┬─────────────┘                                                       │
                │                                                                     │
                ▼                                                                     │
  ┌───────────────────────────┐                                                       │
  │ Notifikasi ke PIMPINAN    │                                                       │
  └─────────────┬─────────────┘                                                       │
                │                                                                     │
                ▼                                                                     │
        /───────────────\                                                             │
       < Validasi Pimpinan >                                                          │
        \───────────────/                                                             │
          │           │                                                               │
   [Setuju]           [Tolak]                                                         │
      │               │                                                               │
      ▼               ▼                                                               │
┌─────────────┐ ┌─────────────┐                                                       │
│ Status:     │ │ Status:     │                                                       │
│ "Disetujui" │ │ "Ditolak"   │                                                       │
└──────┬──────┘ └─────────────┘                                                       │
       │                                                                              │
       ▼                                                                              │
┌─────────────────────────────┐                                                       │
│ Notifikasi ke UNIT KERJA    │                                                       │
└──────┬──────────────────────┘                                                       │
       │                                                                              │
       └──────────────────────────────────┬───────────────────────────────────────────┘
                                          │
                                          ▼
                       ┌─────────────────────────────────────┐
                       │ STATUS DOKUMEN REPOSITORI BERUBAH:  │
                       │       "Dalam Perpanjangan"          │
                       └──────────────────┬──────────────────┘
                                          │
                                          ▼
                       ┌─────────────────────────────────────┐
                       │ Pemrosesan Draf & Unggah Berkas Baru│
                       │        (Unit Kerja / Admin)         │
                       └──────────────────┬──────────────────┘
                                          │
                                          ▼
                       ┌─────────────────────────────────────┐
                       │ STATUS DOKUMEN BARU DI REPOSITORI:  │
                       │              "Aktif"                │
                       └─────────────────────────────────────┘
```

---

## 14.4 Diagram Alur Proses Detail (End-to-End User Flow)

```
╔══════════════════════════════════════════════════════════════╗
║                     HALAMAN WELCOME                         ║
║                                                              ║
║    User membuka halaman utama sistem (route: /)              ║
║    ├── Menekan tombol "Ajukan Kerja Sama"                    ║
║    └── Muncul Modal Pilihan:                                 ║
║        ├── [1] Ajukan Kerja Sama Baru → /pengajuan-mitra     ║
║        └── [2] Perpanjang Kerja Sama → /perpanjangan-kerjasama║
║                         │                                    ║
╚═════════════════════════╪════════════════════════════════════╝
                          │
                          ▼
╔══════════════════════════════════════════════════════════════╗
║              HALAMAN PERPANJANGAN KERJA SAMA                 ║
║              (auth/perpanjangan.blade.php)                    ║
║                                                              ║
║  ┌─────────────────────────────────────────────┐             ║
║  │  HERO SECTION                                │             ║
║  │  Penjelasan 4 poin utama langkah pengajuan  │             ║
║  └─────────────────────────────────────────────┘             ║
║                          │                                    ║
║  ┌─────────────────────────────────────────────┐             ║
║  │  STEPPER PROGRESS TRACKER                    │             ║
║  │  [1.Mitra] → [2.Kontak] → [3.Rencana]       │             ║
║  │           → [4.Tinjau] → [5.Kirim]           │             ║
║  │  + Label Langkah + Persentase (0%–100%)     │             ║
║  └─────────────────────────────────────────────┘             ║
║                          │                                    ║
║  ┌───────────────────────┼───────────────────────┐           ║
║  │              WIZARD FORM (5 STEP)              │           ║
║  │                       │                        │           ║
║  │   ╔═══════════════════╧══════════════════╗     │           ║
║  │   ║  STEP 1: Mitra Terdaftar             ║     │           ║
║  │   ║  • Pilih Mitra (dropdown search)     ║     │           ║
║  │   ║  • Pilih Jenis Dokumen (MoU/MoA/IA)  ║     │           ║
║  │   ║  • Nomor Dokumen Lama                ║     │           ║
║  │   ╚═══════════════════╤══════════════════╝     │           ║
║  │                       │ [Validasi → Selanjutnya]│          ║
║  │   ╔═══════════════════╧══════════════════╗     │           ║
║  │   ║  STEP 2: Kontak Terbaru              ║     │           ║
║  │   ║  • Nama Penandatangan *              ║     │           ║
║  │   ║  • Jabatan Penandatangan *           ║     │           ║
║  │   ║  • Nama Penanggung Jawab             ║     │           ║
║  │   ║  • Jabatan Penanggung Jawab          ║     │           ║
║  │   ╚═══════════════════╤══════════════════╝     │           ║
║  │                       │ [Validasi → Selanjutnya]│          ║
║  │   ╔═══════════════════╧══════════════════╗     │           ║
║  │   ║  STEP 3: Rencana Lanjutan            ║     │           ║
║  │   ║  • Periode Kerjasama (Mulai–Selesai)*║     │           ║
║  │   ║  • Judul Rencana Perpanjangan *      ║     │           ║
║  │   ║  • Tujuan Perpanjangan *             ║     │           ║
║  │   ║  • Ruang Lingkup Lanjutan *          ║     │           ║
║  │   ║  • Upload Surat Permohonan (.pdf)*   ║     │           ║
║  │   ╚═══════════════════╤══════════════════╝     │           ║
║  │                       │ [Validasi + syncReview] │           ║
║  │   ╔═══════════════════╧══════════════════╗     │           ║
║  │   ║  STEP 4: Tinjau Perpanjangan         ║     │           ║
║  │   ║  Review Card 1: Identitas Mitra      ║     │           ║
║  │   ║  Review Card 2: Kontak Terkini       ║     │           ║
║  │   ║  Review Card 3: Rencana Perpanjangan ║     │           ║
║  │   ╚═══════════════════╤══════════════════╝     │           ║
║  │                       │ [Selanjutnya]           │           ║
║  │   ╔═══════════════════╧══════════════════╗     │           ║
║  │   ║  STEP 5: Konfirmasi Akhir            ║     │           ║
║  │   ║  • Checkbox Pernyataan Data *         ║     │           ║
║  │   ║  • Catatan Tambahan (opsional)        ║     │           ║
║  │   ║  • Tombol [Kirim Data]                ║     │           ║
║  │   ╚═══════════════════╤══════════════════╝     │           ║
║  └───────────────────────┼───────────────────────┘           ║
╚══════════════════════════╪═══════════════════════════════════╝
                           │
                           ▼
╔══════════════════════════════════════════════════════════════╗
║                PROSES SERVER-SIDE                            ║
║                                                              ║
║  1. POST /perpanjangan-kerjasama                             ║
║     Controller: PublicPengajuanKerjasamaController            ║
║                 ::storePerpanjangan()                         ║
║                                                              ║
║  2. Validasi Server (Laravel Validation):                    ║
║     ├── mitra_id     → required, exists:mitras,id            ║
║     ├── jenis        → required, string, max:255             ║
║     ├── doc_number   → required, string, max:255             ║
║     ├── nama_penandatangan → required, string, max:255       ║
║     ├── jabatan_penandatangan → required, string, max:255    ║
║     ├── nama_penanggung_jawab → nullable, string, max:255    ║
║     ├── jabatan_penanggung_jawab → nullable, string, max:255 ║
║     ├── email        → nullable, email, max:255              ║
║     ├── start_date   → required, date                        ║
║     ├── end_date     → required, date, after_or_equal:start  ║
║     ├── judul_pengajuan → required, string, max:255          ║
║     ├── tujuan_pengajuan → required, string                  ║
║     ├── ruang_lingkup → required, string                     ║
║     ├── file_surat   → required, file, mimes:pdf,doc,docx    ║
║     └── pesan_tambahan → nullable, string                    ║
║                                                              ║
║  3. Jika LOLOS validasi:                                     ║
║     a. Simpan file_surat ke storage/app/public/surat_permohonan║
║     b. Generate kode pengajuan (PGM-YYYYMMDD-XXXX)          ║
║     c. Insert ke tabel pengajuan_kerjasama_mitras (status="diajukan")
║     d. Kirim Notifikasi ke SEMUA Pimpinan                    ║
║                                                              ║
╚══════════════════════════╪═══════════════════════════════════╝
                           │
                           ▼
╔══════════════════════════════════════════════════════════════╗
║              KONFIRMASI & NOTIFIKASI USER                    ║
║                                                              ║
║  • Flash message "success" ditampilkan                       ║
║  • SweetAlert2 popup muncul:                                 ║
║    ├── Icon: success                                         ║
║    ├── Title: "Pengajuan Perpanjangan Berhasil Dikirim!"     ║
║    ├── Body: pesan + kode pengajuan                          ║
║    ├── Badge: "Status: Dalam Proses Validasi Pimpinan"       ║
║    └── Button: "Saya Mengerti"                               ║
║                                                              ║
╚══════════════════════════╪═══════════════════════════════════╝
                           │
                           ▼
╔══════════════════════════════════════════════════════════════╗
║       PROSES VALIDASI PIMPINAN & INTEGRASI REPOSITORI        ║
║              (route: pimpinan.pengajuan_mitra)                ║
║                                                              ║
║  Pimpinan menerima notifikasi dalam sistem                   ║
║     │                                                        ║
║     ▼                                                        ║
║  Pimpinan meninjau detail pengajuan perpanjangan             ║
║     │                                                        ║
║     ├── DISETUJUI                                            ║
║     │   ├── Status Pengajuan → "disetujui"                   ║
║     │   ├── Notifikasi terkirim ke UNIT KERJA                ║
║     │   └── Status Dokumen di Repositori otomatis berubah     ║
║     │       menjadi "Dalam Perpanjangan"                     ║
║     │                                                        ║
║     └── DITOLAK                                              ║
║         ├── Status Pengajuan → "ditolak"                     ║
║         ├── Email penolakan dikirim ke Mitra                 ║
║         └── Status Dokumen di Repositori tetap "Kadaluarsa"  ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
```

---

## 14.5 Langkah-Langkah Detail (Numbered Flow Steps)

### Fase A — Inisiasi Pengajuan Perpanjangan (Pintu 1: Publik vs Pintu 2: Repositori)

| No | Langkah | Aktor | Detail |
|----|---------|-------|--------|
| 1a | **Jalur Publik (Pintu 1):** Akses `/perpanjangan-kerjasama` | Mitra / Publik | Buka form wizard publik 5 step tanpa login |
| 1b | **Jalur Repositori (Pintu 2):** Klik "Perpanjang Kerja Sama" | Unit Kerja / Admin | Buka menu Repositori Kerja Sama -> Detail Dokumen berstatus `Kadaluarsa` -> Klik tombol "Perpanjang Kerja Sama" |
| 2 | Sistem Menyiapkan Data | System | Controller `createPerpanjangan()` memuat data `Mitra`, `JenisKerjasama`, dan memetakan dokumen kerja sama sebelumnya |

### Fase B — Pengisian Form Wizard Publik (5 Langkah)

| No | Langkah | Aktor | Detail |
|----|---------|-------|--------|
| 3 | **Step 1:** Pilih Mitra Terdaftar | User | Dropdown searchable dari Master Mitra. Pilih jenis dokumen (MoU/MoA/IA) dan isi nomor dokumen lama |
| 4 | **Step 2:** Isi Kontak Terbaru | User | Input nama & jabatan penandatangan (wajib), serta nama & jabatan penanggung jawab (opsional) |
| 5 | **Step 3:** Rencana & Unggah Surat | User | Isi tanggal mulai & selesai, judul, tujuan, ruang lingkup, serta **unggah Surat Permohonan Perpanjangan** (.pdf/.doc/.docx max 5MB) |
| 6 | **Step 4:** Tinjau Data | User | Memeriksa 3 card ringkasan: Identitas Mitra, Kontak Terkini, & Rencana Perpanjangan |
| 7 | **Step 5:** Konfirmasi & Kirim | User | Centang persetujuan kebenaran data (wajib) -> Klik "Kirim Data" |

### Fase C — Pemrosesan Server, Validasi Pimpinan, & Update Repositori

| No | Langkah | Aktor | Detail |
|----|---------|-------|--------|
| 8 | Validasi & Simpan Server | System | Simpan berkas permohonan ke storage `surat_permohonan`, generate kode `PGM-YYYYMMDD-XXXX`, insert ke `pengajuan_kerjasama_mitras` status `"diajukan"` |
| 9 | Notifikasi ke Pimpinan | System | Memicu `Notifikasi::send()` ke semua user ber-role `pimpinan` |
| 10 | Validasi Pimpinan | Pimpinan | Pimpinan meninjau berkas & detail perpanjangan di `/pimpinan/pengajuan-mitra` |
| 11a| **Persetujuan (Disetujui)** | Pimpinan | Status pengajuan = `"disetujui"`. Notifikasi dikirim ke **Unit Kerja**. **Status dokumen di Repositori otomatis berubah dari `Kadaluarsa` menjadi `Dalam Perpanjangan`** |
| 11b| **Penolakan (Ditolak)** | Pimpinan | Status pengajuan = `"ditolak"`. Status dokumen di Repositori tetap `"Kadaluarsa"`. Email penolakan terkirim ke Mitra |
| 12 | Penyelesaian Dokumen Baru | Unit Kerja | Unit Kerja memproses berkas PKS/MoU perpanjangan baru. Setelah ditandatangani & diunggah, dokumen baru terbit dengan status **`Aktif`** |

---

## 14.4 Aktor yang Terlibat

### User / Pemohon (Pihak Mitra)
- **Akses:** Halaman publik (tanpa login)
- **Kemampuan:**
  - Membuka halaman welcome dan memilih opsi perpanjangan
  - Mengisi wizard form perpanjangan kerja sama (5 langkah)
  - Melihat review/ringkasan data sebelum pengiriman
  - Menerima konfirmasi pengiriman (kode pengajuan + status)

### Pimpinan (Approver)
- **Akses:** Dashboard internal (memerlukan login, role: `pimpinan`)
- **Kemampuan:**
  - Menerima notifikasi pengajuan perpanjangan baru
  - Meninjau detail lengkap pengajuan
  - Menyetujui atau menolak pengajuan
  - Memberikan catatan/alasan keputusan

### System (Automated)
- **Tanggung Jawab:**
  - Validasi data di client-side (per-step JavaScript) dan server-side (Laravel)
  - Generate kode pengajuan unik (`PGM-YYYYMMDD-XXXX`)
  - Auto-fill data mitra dari Master Mitra berdasarkan `mitra_id`
  - Pengiriman notifikasi ke semua Pimpinan
  - Manajemen transaksi database (commit/rollback)
  - Tampilan konfirmasi sukses (SweetAlert2)

### Humas / Unit Kerja (Post-Approval)
- **Akses:** Dashboard internal (memerlukan login)
- **Kemampuan (setelah Pimpinan menyetujui):**
  - Melengkapi data mitra
  - Mengunggah dokumen kerja sama
  - Mengaktifkan status kerja sama

---

## 14.5 Status Lifecycle Perpanjangan

```
                    ┌────────────────┐
                    │    DIAJUKAN    │ ← Status awal saat form dikirim
                    │  (submitted)   │
                    └───────┬────────┘
                            │
                    Pimpinan Review
                            │
               ┌────────────┼────────────┐
               │                         │
      ┌────────▼─────────┐     ┌─────────▼────────┐
      │     DITOLAK      │     │    DISETUJUI     │
      │   (rejected)     │     │   (approved)     │
      └──────────────────┘     └────────┬─────────┘
                                        │
                                  Humas Proses
                                        │
                               ┌────────▼─────────┐
                               │     PROSES       │
                               │  (in progress)   │
                               └────────┬─────────┘
                                        │
                                  Data Lengkap?
                                   │        │
                                 Tidak      Ya
                                   │        │
                                   └───┐    │
                                       │    │
                               ┌───────▼────▼──────┐
                               │      AKTIF        │
                               │    (active)       │
                               └───────────────────┘
```

---

## 14.6 Data Fields yang Dikumpulkan

### Step 1 — Mitra Terdaftar

| Field | Type | Required | Sumber Data |
|-------|------|----------|-------------|
| `mitra_id` | Select (dropdown search) | ✅ | Tabel `mitra` |
| `jenis` | Hidden (via Alpine.js) | ✅ | Pilihan: MoU / MoA / IA |
| `doc_number` | Text | ✅ | Input manual |

### Step 2 — Kontak Terbaru

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `nama_penandatangan` | Text | ✅ | Nama lengkap |
| `jabatan_penandatangan` | Text | ✅ | Contoh: Direktur |
| `nama_penanggung_jawab` | Text | ❌ | Opsional |
| `jabatan_penanggung_jawab` | Text | ❌ | Opsional |
| `email` | Email | ✅ | Email aktif PIC |

### Step 3 — Rencana Lanjutan

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `start_date` | Date (custom datepicker) | ✅ | Tanggal mulai periode baru |
| `end_date` | Date (custom datepicker) | ✅ | Harus ≥ start_date |
| `judul_pengajuan` | Text | ✅ | Judul rencana perpanjangan |
| `tujuan_pengajuan` | Textarea | ✅ | Alasan & target perpanjangan |
| `ruang_lingkup` | Select (dropdown search) | ✅ | Dari tabel `jenis_kerjasama` |

### Step 5 — Konfirmasi

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `declaration_agree` | Checkbox | ✅ | Pernyataan kebenaran data |
| `pesan_tambahan` | Textarea | ❌ | Catatan opsional |

---

## 14.7 Mekanisme Validasi

### Client-Side Validation (JavaScript)

Validasi dilakukan **per langkah** sebelum user berpindah ke step berikutnya melalui fungsi `validateCurrentStep()`:

1. **Cek semua field `[required]`** pada step aktif
2. **Checkbox** → dicek apakah `checked`
3. **Input/select/textarea** → `checkValidity()` (HTML5 validation)
4. **Jika invalid:**
   - Class `has-error` ditambahkan pada parent `.partner-field`
   - Focus otomatis ke field invalid pertama
   - Smooth scroll ke field tersebut
5. **Realtime cleanup:** Event `input` dan `change` langsung menghapus class `has-error` saat user mulai mengisi

### Server-Side Validation (Laravel)

Validasi menyeluruh dilakukan di `storePerpanjangan()`:

- `mitra_id` → harus ada di tabel `mitra`
- `email` → format email valid
- `end_date` → harus `after_or_equal:start_date`
- Semua field wajib dicek ulang
- Jika gagal: redirect back dengan `withInput()` + error messages per field (`@error` directive)

---

## 14.8 Catatan Tambahan & Rekomendasi UI/UX

### ✅ Hal Positif yang Sudah Ada

| Aspek | Detail |
|-------|--------|
| **Wizard Multi-Step** | UX sangat baik — membagi form panjang menjadi 5 langkah kecil sehingga tidak overwhelming |
| **Progress Tracker** | Stepper visual dengan progress fill dan persentase membantu user tahu posisi mereka |
| **Review Step** | Step 4 (Tinjau) sangat penting — mencegah kesalahan data sebelum submit |
| **Search Dropdown** | Dropdown mitra dan ruang lingkup mendukung pencarian, bagus untuk dataset besar |
| **Dark Mode** | Tersedia toggle dark/light mode yang disimpan di localStorage |
| **Realtime Error Cleanup** | Error highlight langsung hilang saat user mulai mengisi field — UX responsif |
| **SweetAlert2 Confirmation** | Popup sukses yang informatif dengan badge status dan theming yang konsisten |
| **Datepicker Custom** | Datepicker yang dibangun sendiri dengan Alpine.js, mendukung navigasi bulan/tahun dan search |

### 🔧 Rekomendasi Perbaikan

#### 1. Auto-Fill Data Mitra ke Step Lain
**Masalah:** Saat mitra dipilih di Step 1, data kontak lama (PIC, email) tidak otomatis terisi di Step 2.
**Rekomendasi:** Tambahkan AJAX call atau data attribute untuk auto-populate kontak mitra yang sudah ada ke Step 2 sebagai nilai default, sehingga user hanya perlu memperbarui jika ada perubahan.

#### 2. Validasi End Date > Start Date di Client-Side
**Masalah:** Validasi `end_date` harus setelah `start_date` hanya dilakukan di server.
**Rekomendasi:** Tambahkan validasi client-side di `validateCurrentStep()` untuk Step 3 — bandingkan kedua tanggal dan tampilkan pesan error inline jika end_date < start_date.

#### 3. Konfirmasi Sebelum Submit (Step 5)
**Masalah:** Tombol "Kirim Data" langsung submit tanpa konfirmasi dialog.
**Rekomendasi:** Tambahkan SweetAlert2 confirmation dialog ("Apakah Anda yakin ingin mengirim pengajuan ini?") sebelum form benar-benar di-submit, untuk mencegah klik tidak sengaja.

#### 4. Indikator Loading saat Submit
**Masalah:** Tidak ada loading indicator saat proses submit ke server.
**Rekomendasi:** Disable tombol "Kirim Data" dan tampilkan spinner/loading text setelah diklik untuk mencegah double-submit.

#### 5. Navigasi Step via Klik Stepper
**Masalah:** User hanya bisa navigasi via tombol "Sebelumnya/Selanjutnya", tidak bisa klik langsung pada stepper circle.
**Rekomendasi:** Izinkan klik pada step yang sudah completed (is-completed) untuk navigasi cepat ke step sebelumnya. Step yang belum dilalui tetap tidak bisa diklik.

#### 6. Informasi Mitra di Review Card
**Masalah:** Di Step 4 (Tinjau), hanya menampilkan nama mitra yang dipilih.
**Rekomendasi:** Tambahkan informasi pendukung seperti kategori mitra (Nasional/Internasional), negara, dan klasifikasi di review card agar Pimpinan juga bisa melihat konteks lebih lengkap.

#### 7. Tracking Status oleh User
**Masalah:** Setelah submit, user tidak memiliki cara untuk melihat status pengajuannya secara mandiri.
**Rekomendasi:** Sediakan halaman publik untuk cek status menggunakan kode pengajuan (PGM-XXXXXX-XXXX), tanpa perlu login. Halaman ini menampilkan timeline progress: Diajukan → Ditinjau → Disetujui/Ditolak.

#### 8. Email Notifikasi ke Pemohon
**Masalah:** Saat ini hanya ada notifikasi in-app ke Pimpinan, tidak ada email konfirmasi ke pemohon.
**Rekomendasi:** Kirim email otomatis ke alamat email yang diisi di Step 2 berisi:
- Kode pengajuan
- Ringkasan data yang diajukan
- Estimasi waktu review
- Link untuk cek status (jika fitur #7 diimplementasi)

#### 9. Durasi Periode Otomatis
**Masalah:** User harus menghitung sendiri berapa lama periode kerjasama baru.
**Rekomendasi:** Tambahkan kalkulasi otomatis yang menampilkan durasi kerjasama (misal: "2 tahun 3 bulan") secara real-time saat kedua tanggal dipilih.

#### 10. Responsive & Accessibility
**Masalah:** Beberapa inline style pada grid dan layout mungkin tidak optimal di mobile.
**Rekomendasi:**
- Audit layout grid pada layar < 480px (terutama grid 2 kolom di Step 1 untuk jenis dokumen + nomor)
- Pastikan datepicker dropdown tidak keluar dari viewport di mobile
- Tambahkan `aria-label` pada tombol navigasi wizard untuk screen reader