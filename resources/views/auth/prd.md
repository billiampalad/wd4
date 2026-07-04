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