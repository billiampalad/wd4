# 📊 Analysis DFD (Data Flow Diagram) — Sistem Informasi Kerja Sama Kampus–DUDIKA (WD4)

> **Versi**: 1.0 — Dokumen Analisis Data Flow Diagram  
> **Tanggal**: 30 Juli 2026  
> **Referensi**: [analysis-use-case.md](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-use-case.md) | [planning.md](file:///c:/laragon/www/wd4/pengembangan-sistem/planning.md) | [skils-dfd.md](file:///c:/laragon/www/wd4/skils-diagram/skils-dfd.md)

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Identifikasi Komponen DFD](#2-identifikasi-komponen-dfd)
3. [Context Diagram](#3-context-diagram)
4. [DFD Level 0](#4-dfd-level-0)
5. [DFD Level 1 — Mengelola Data Master](#5-dfd-level-1--mengelola-data-master)
6. [DFD Level 1 — Mengelola Dokumen Kerja Sama](#6-dfd-level-1--mengelola-dokumen-kerja-sama)
7. [DFD Level 1 — Mengelola Pengajuan Kerja Sama](#7-dfd-level-1--mengelola-pengajuan-kerja-sama)
8. [DFD Level 1 — Memvalidasi Dokumen dan Pengajuan](#8-dfd-level-1--memvalidasi-dokumen-dan-pengajuan)
9. [DFD Level 1 — Mengelola Kegiatan dan Monitoring](#9-dfd-level-1--mengelola-kegiatan-dan-monitoring)
10. [DFD Level 1 — Mengelola Evaluasi](#10-dfd-level-1--mengelola-evaluasi)
11. [DFD Level 1 — Mengelola Tracking Lulusan](#11-dfd-level-1--mengelola-tracking-lulusan)
12. [DFD Level 1 — Membuat Laporan dan Dashboard](#12-dfd-level-1--membuat-laporan-dan-dashboard)
13. [Ringkasan Balancing DFD](#13-ringkasan-balancing-dfd)
14. [Checklist Validasi DFD](#14-checklist-validasi-dfd)

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen

Dokumen ini menggambarkan **Data Flow Diagram (DFD)** untuk Sistem Informasi Kerja Sama Kampus–DUDIKA (WD4). DFD digunakan untuk menjelaskan bagaimana data mengalir dari satu proses ke proses lain, dari mana data berasal, dan ke mana data ditujukan.

### 1.2 Fokus DFD

DFD dalam dokumen ini menjawab pertanyaan:

```text
1. Dari mana data berasal?          → External Entity
2. Data apa yang mengalir?          → Data Flow
3. Proses apa yang mengolah data?   → Process
4. Di mana data disimpan?           → Data Store
5. Ke mana data/informasi dikirim?  → External Entity
```

### 1.3 Struktur Level DFD

```text
Context Diagram    → Gambaran umum seluruh sistem (1 proses utama)
        ↓
DFD Level 0        → Proses-proses utama sistem (8 proses)
        ↓
DFD Level 1        → Detail setiap proses utama (dekomposisi)
```

### 1.4 Konvensi Penamaan

| Komponen | Aturan Penamaan | Contoh |
|----------|----------------|--------|
| **External Entity** | Kata benda (role/pihak luar) | Admin, Pimpinan, Mitra |
| **Process** | Kata kerja + objek | Mengelola Data Mitra |
| **Data Flow** | Nama data (kata benda) | Data Pengajuan, Hasil Validasi |
| **Data Store** | Kode D + Nama kumpulan data | D1 Data Pengguna |

---

## 2. Identifikasi Komponen DFD

### 2.1 External Entity

Pihak di luar sistem yang memberikan data ke atau menerima data dari sistem:

| Kode | External Entity | Deskripsi | Data yang Dikirim | Data yang Diterima |
|------|----------------|-----------|-------------------|-------------------|
| E1 | **Admin** | Pengelola sistem & data master | Data Pengguna, Data Role, Data Klasifikasi, Data Unit, Credential Mitra | Konfirmasi Penyimpanan, Notifikasi Sistem |
| E2 | **Pimpinan** | Pengambil keputusan strategis | Data Validasi, Data Evaluasi, Keputusan Persetujuan | Laporan Eksekutif, Notifikasi Pengajuan, Statistik Penyerapan |
| E3 | **Humas** | Administrasi & komunikasi mitra | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| E4 | **Jurusan** | Pengelola KS tingkat jurusan | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit, Statistik Lulusan |
| E5 | **Prodi** | Pengelola kegiatan mahasiswa | Data Kegiatan, Data Mahasiswa, Data Lulusan | Konfirmasi Penempatan, Notifikasi, Statistik Lulusan |
| E6 | **UPA** | Pengelola KS unit pelaksana | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| E7 | **Pusat** | Pengelola KS tingkat pusat | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| E8 | **Mitra** | Pihak industri eksternal | Data Pengajuan KS, Data Penilaian MHS, Data Lulusan, Data Umpan Balik, Data Perpanjangan | Informasi Dokumen KS, Notifikasi, Credential Login, Statistik Lulusan |

---

### 2.2 Data Store

Tempat penyimpanan data yang digunakan oleh sistem:

| Kode | Data Store | Deskripsi | Tabel Database Terkait |
|------|-----------|-----------|----------------------|
| D1 | Data Pengguna | Data akun, profil, dan role pengguna | `users`, `profiles`, `roles` |
| D2 | Data Mitra | Data instansi mitra/DUDIKA | `mitras`, `klasifikasis` |
| D3 | Data Dokumen Kerja Sama | Data dokumen MoU, MoA, IA/SPK | `cooperations`, `laporan_files`, `pks_numbers` |
| D4 | Data Kegiatan | Data kegiatan pelaksanaan KS | `kegiatan_kerjasamas`, `detail_kegiatans` |
| D5 | Data Mahasiswa | Data mahasiswa peserta kegiatan | `mahasiswas`, `kegiatan_mahasiswas`, `pembimbings` |
| D6 | Data Evaluasi | Data evaluasi & umpan balik KS | `evaluasis` |
| D7 | Data Alumni | Data lulusan & relasi dengan mitra | `alumnis`, `alumni_mitras` |
| D8 | Data Notifikasi | Data notifikasi sistem | `notifikasis` |
| D9 | Data Unit | Data jurusan, prodi, UPA, pusat | `jurusans`, `prodis`, `upas`, `pusats` |
| D10 | Data Referensi | Data jenis KS, sasaran, indikator | `jenis_kerjasamas`, `sasarans`, `indikators` |

---

### 2.3 Proses Utama (Level 0)

| Kode | Proses | Deskripsi |
|------|--------|-----------|
| P1 | Mengelola Data Master | Mengelola data pengguna, role, mitra, unit, klasifikasi, dan referensi |
| P2 | Mengelola Dokumen Kerja Sama | Menginput, mengedit, dan mensubmit dokumen MoU/MoA/IA |
| P3 | Mengelola Pengajuan Kerja Sama | Memproses pengajuan dan perpanjangan KS dari mitra |
| P4 | Memvalidasi Dokumen dan Pengajuan | Pimpinan memvalidasi dan mengesahkan dokumen serta pengajuan |
| P5 | Mengelola Kegiatan dan Monitoring | Menginput kegiatan, peserta mahasiswa, dan penilaian |
| P6 | Mengelola Evaluasi | Mengisi, mensubmit, dan memvalidasi evaluasi KS |
| P7 | Mengelola Tracking Lulusan | Menginput dan mengelola data penyerapan lulusan di mitra |
| P8 | Membuat Laporan dan Dashboard | Menghasilkan dashboard, laporan, dan analitik |

---

## 3. Context Diagram

Context Diagram menggambarkan sistem secara keseluruhan sebagai **satu proses tunggal** yang berinteraksi dengan semua external entity.

### 3.1 Diagram

```mermaid
graph TB
    E1["🔧 Admin"]
    E2["👔 Pimpinan"]
    E3["🏢 Humas"]
    E4["🎓 Jurusan"]
    E5["📚 Prodi"]
    E6["🏛 UPA"]
    E7["🔬 Pusat"]
    E8["🏭 Mitra"]

    SISTEM(("0\nSistem Informasi\nKerja Sama\nKampus–DUDIKA\n(WD4)"))

    E1 -- "Data Pengguna,\nData Role,\nData Klasifikasi,\nData Unit,\nCredential Mitra" --> SISTEM
    SISTEM -- "Konfirmasi Penyimpanan,\nNotifikasi Sistem,\nLaporan" --> E1

    E2 -- "Data Validasi,\nKeputusan Persetujuan,\nData Evaluasi" --> SISTEM
    SISTEM -- "Laporan Eksekutif,\nNotifikasi Pengajuan,\nStatistik Penyerapan,\nDaftar Dokumen Menunggu" --> E2

    E3 -- "Data Dokumen KS,\nData Kegiatan,\nData Evaluasi,\nData Mitra" --> SISTEM
    SISTEM -- "Konfirmasi Status,\nNotifikasi,\nLaporan Unit" --> E3

    E4 -- "Data Dokumen KS,\nData Kegiatan,\nData Evaluasi,\nData Mitra" --> SISTEM
    SISTEM -- "Konfirmasi Status,\nNotifikasi,\nLaporan Unit,\nStatistik Lulusan" --> E4

    E5 -- "Data Kegiatan,\nData Mahasiswa,\nData Lulusan" --> SISTEM
    SISTEM -- "Konfirmasi Penempatan,\nNotifikasi,\nStatistik Lulusan" --> E5

    E6 -- "Data Dokumen KS,\nData Kegiatan,\nData Evaluasi,\nData Mitra" --> SISTEM
    SISTEM -- "Konfirmasi Status,\nNotifikasi,\nLaporan Unit" --> E6

    E7 -- "Data Dokumen KS,\nData Kegiatan,\nData Evaluasi,\nData Mitra" --> SISTEM
    SISTEM -- "Konfirmasi Status,\nNotifikasi,\nLaporan Unit" --> E7

    E8 -- "Data Pengajuan KS,\nData Penilaian MHS,\nData Lulusan,\nData Umpan Balik,\nData Perpanjangan" --> SISTEM
    SISTEM -- "Informasi Dokumen KS,\nNotifikasi,\nCredential Login,\nStatistik Lulusan" --> E8
```

### 3.2 Tabel Aliran Data Context Diagram

| External Entity | Data Flow Masuk (→ Sistem) | Data Flow Keluar (Sistem →) |
|-----------------|---------------------------|----------------------------|
| Admin | Data Pengguna, Data Role, Data Klasifikasi, Data Unit, Credential Mitra | Konfirmasi Penyimpanan, Notifikasi Sistem, Laporan |
| Pimpinan | Data Validasi, Keputusan Persetujuan, Data Evaluasi | Laporan Eksekutif, Notifikasi Pengajuan, Statistik Penyerapan, Daftar Dokumen Menunggu |
| Humas | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| Jurusan | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit, Statistik Lulusan |
| Prodi | Data Kegiatan, Data Mahasiswa, Data Lulusan | Konfirmasi Penempatan, Notifikasi, Statistik Lulusan |
| UPA | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| Pusat | Data Dokumen KS, Data Kegiatan, Data Evaluasi, Data Mitra | Konfirmasi Status, Notifikasi, Laporan Unit |
| Mitra | Data Pengajuan KS, Data Penilaian MHS, Data Lulusan, Data Umpan Balik, Data Perpanjangan | Informasi Dokumen KS, Notifikasi, Credential Login, Statistik Lulusan |

---

## 4. DFD Level 0

DFD Level 0 memecah sistem menjadi **8 proses utama** beserta data store dan aliran data antar proses.

### 4.1 Diagram

```mermaid
graph TB
    %% External Entities
    E1["🔧 Admin"]
    E2["👔 Pimpinan"]
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E5["📚 Prodi"]
    E8["🏭 Mitra"]

    %% Processes
    P1(("1\nMengelola\nData Master"))
    P2(("2\nMengelola\nDokumen\nKerja Sama"))
    P3(("3\nMengelola\nPengajuan\nKerja Sama"))
    P4(("4\nMemvalidasi\nDokumen &\nPengajuan"))
    P5(("5\nMengelola\nKegiatan &\nMonitoring"))
    P6(("6\nMengelola\nEvaluasi"))
    P7(("7\nMengelola\nTracking\nLulusan"))
    P8(("8\nMembuat\nLaporan &\nDashboard"))

    %% Data Stores
    D1[("D1 Data Pengguna")]
    D2[("D2 Data Mitra")]
    D3[("D3 Data Dokumen KS")]
    D4[("D4 Data Kegiatan")]
    D5[("D5 Data Mahasiswa")]
    D6[("D6 Data Evaluasi")]
    D7[("D7 Data Alumni")]
    D8[("D8 Data Notifikasi")]
    D9[("D9 Data Unit")]
    D10[("D10 Data Referensi")]

    %% Admin → P1
    E1 -- "Data Pengguna,\nData Role,\nData Klasifikasi,\nData Unit" --> P1
    P1 -- "Konfirmasi\nPenyimpanan" --> E1

    %% P1 ↔ Data Stores
    P1 --> D1
    P1 --> D2
    P1 --> D9
    P1 --> D10
    D1 --> P1
    D2 --> P1
    D9 --> P1

    %% Admin → P1 (Credential Mitra)
    E1 -- "Credential Mitra" --> P1
    P1 -- "Credential Login" --> E8

    %% Humas/Jurusan/UPA/Pusat → P2
    E34 -- "Data Dokumen KS,\nData Mitra" --> P2
    P2 -- "Konfirmasi Status\nDokumen" --> E34

    %% P2 ↔ Data Stores
    P2 --> D3
    D3 --> P2
    D2 --> P2

    %% P2 → P4 (Submit ke Pimpinan)
    P2 -- "Dokumen Disubmit" --> P4

    %% Mitra → P3
    E8 -- "Data Pengajuan KS,\nData Perpanjangan" --> P3
    P3 -- "Konfirmasi\nPengajuan" --> E8

    %% P3 ↔ Data Stores
    P3 --> D3
    D2 --> P3

    %% P3 → P4 (Pengajuan ke Pimpinan)
    P3 -- "Pengajuan Baru" --> P4
    P3 -- "Notifikasi Pengajuan" --> D8

    %% P4 ↔ Pimpinan
    E2 -- "Data Validasi,\nKeputusan" --> P4
    P4 -- "Daftar Menunggu\nValidasi" --> E2

    %% P4 ↔ Data Stores
    D3 --> P4
    P4 --> D3
    P4 --> D1
    P4 -- "Notifikasi Hasil" --> D8

    %% P4 → External (Notifikasi)
    P4 -- "Notifikasi\nRevisi" --> E34
    P4 -- "Notifikasi\nPersetujuan" --> E8

    %% Humas/Jurusan/UPA/Pusat/Prodi → P5
    E34 -- "Data Kegiatan" --> P5
    E5 -- "Data Kegiatan,\nData Mahasiswa" --> P5
    E8 -- "Data Penilaian\nMahasiswa" --> P5

    %% P5 ↔ Data Stores
    P5 --> D4
    P5 --> D5
    D3 --> P5
    D4 --> P5
    D5 --> P5
    D2 --> P5

    %% P5 → External (Monitoring)
    P5 -- "Data Monitoring\nMahasiswa" --> E2
    P5 -- "Konfirmasi\nPenempatan" --> E5
    P5 -- "Notifikasi Penilaian" --> D8

    %% Humas/Jurusan/UPA/Pusat → P6
    E34 -- "Data Evaluasi" --> P6
    E2 -- "Data Validasi\nEvaluasi" --> P6
    E8 -- "Data Umpan Balik" --> P6

    %% P6 ↔ Data Stores
    P6 --> D6
    D6 --> P6
    D3 --> P6
    P6 -- "Notifikasi Evaluasi" --> D8

    %% P6 → External
    P6 -- "Konfirmasi\nEvaluasi" --> E34
    P6 -- "Hasil Validasi\nEvaluasi" --> E2

    %% Prodi/Mitra → P7
    E5 -- "Data Lulusan" --> P7
    E8 -- "Data Lulusan\ndi Mitra" --> P7

    %% P7 ↔ Data Stores
    P7 --> D7
    D7 --> P7
    D2 --> P7
    D9 --> P7

    %% P7 → External
    P7 -- "Statistik\nPenyerapan" --> E5
    P7 -- "Statistik\nPenyerapan" --> E8

    %% Data Stores → P8 (Laporan)
    D3 --> P8
    D4 --> P8
    D5 --> P8
    D6 --> P8
    D7 --> P8
    D2 --> P8

    %% P8 → External Entities
    P8 -- "Laporan Eksekutif,\nStatistik" --> E2
    P8 -- "Laporan Unit" --> E34
    P8 -- "Laporan Unit" --> E5
    P8 -- "Laporan" --> E1

    %% Mitra Review
    E8 -- "Permintaan\nLihat Dokumen" --> P2
    P2 -- "Informasi\nDokumen KS" --> E8

    %% Notifikasi
    D8 --> P8
```

### 4.2 Tabel Aliran Data Level 0

| Dari | Ke | Data Flow | Keterangan |
|------|----|-----------|------------|
| Admin | P1 | Data Pengguna, Data Role, Data Klasifikasi, Data Unit | Input master data |
| P1 | Admin | Konfirmasi Penyimpanan | Feedback operasi CRUD |
| P1 | D1 | Data Pengguna | Simpan data user |
| P1 | D2 | Data Mitra | Simpan data mitra |
| P1 | D9 | Data Unit | Simpan data jurusan/prodi/UPA/pusat |
| P1 | D10 | Data Referensi | Simpan jenis KS, sasaran, indikator |
| Admin | P1 | Credential Mitra | Kirim akses login ke mitra |
| P1 | Mitra | Credential Login | Email berisi username & password |
| Humas/Jurusan/UPA/Pusat | P2 | Data Dokumen KS, Data Mitra | Input dokumen MoU/MoA/IA |
| P2 | Humas/Jurusan/UPA/Pusat | Konfirmasi Status Dokumen | Feedback penyimpanan dokumen |
| P2 | D3 | Data Dokumen KS | Simpan dokumen ke database |
| D2 | P2 | Data Mitra | Baca data mitra untuk relasi |
| P2 | P4 | Dokumen Disubmit | Dokumen yang disubmit ke Pimpinan |
| Mitra | P3 | Data Pengajuan KS, Data Perpanjangan | Pengajuan dari portal mitra |
| P3 | Mitra | Konfirmasi Pengajuan | Feedback pengajuan berhasil |
| P3 | D3 | Data Dokumen KS | Simpan pengajuan sebagai draft |
| P3 | P4 | Pengajuan Baru | Forward pengajuan ke Pimpinan |
| Pimpinan | P4 | Data Validasi, Keputusan | Keputusan validasi/pengesahan |
| P4 | Pimpinan | Daftar Menunggu Validasi | Daftar dokumen/pengajuan pending |
| P4 | D3 | Data Dokumen KS (Update Status) | Update status dokumen |
| P4 | D1 | Data Pengguna (Akun Mitra) | Auto-create akun saat persetujuan |
| P4 | D8 | Notifikasi Hasil | Notifikasi keputusan |
| P4 | Humas/Jurusan/UPA/Pusat | Notifikasi Revisi | Informasi revisi ke unit |
| P4 | Mitra | Notifikasi Persetujuan | Informasi hasil pengajuan |
| Humas/Jurusan/UPA/Pusat | P5 | Data Kegiatan | Input data kegiatan KS |
| Prodi | P5 | Data Kegiatan, Data Mahasiswa | Input kegiatan & peserta MHS |
| Mitra | P5 | Data Penilaian Mahasiswa | Penilaian dari mitra |
| P5 | D4 | Data Kegiatan | Simpan data kegiatan |
| P5 | D5 | Data Mahasiswa | Simpan data penempatan MHS |
| P5 | Pimpinan | Data Monitoring Mahasiswa | Statistik monitoring |
| P5 | Prodi | Konfirmasi Penempatan | Feedback penempatan MHS |
| Humas/Jurusan/UPA/Pusat | P6 | Data Evaluasi | Input evaluasi KS |
| Pimpinan | P6 | Data Validasi Evaluasi | Keputusan validasi evaluasi |
| Mitra | P6 | Data Umpan Balik | Feedback dari mitra |
| P6 | D6 | Data Evaluasi | Simpan data evaluasi |
| P6 | Humas/Jurusan/UPA/Pusat | Konfirmasi Evaluasi | Feedback evaluasi tersimpan |
| P6 | Pimpinan | Hasil Validasi Evaluasi | Hasil keputusan evaluasi |
| Prodi | P7 | Data Lulusan | Input data alumni |
| Mitra | P7 | Data Lulusan di Mitra | Konfirmasi alumni bekerja |
| P7 | D7 | Data Alumni | Simpan relasi alumni-mitra |
| P7 | Prodi/Mitra | Statistik Penyerapan | Data penyerapan lulusan |
| D3, D4, D5, D6, D7, D2 | P8 | Data untuk Laporan | Sumber data laporan |
| P8 | Pimpinan | Laporan Eksekutif, Statistik | Dashboard & laporan |
| P8 | Humas/Jurusan/UPA/Pusat/Prodi | Laporan Unit | Laporan per unit |
| Mitra | P2 | Permintaan Lihat Dokumen | Request akses dokumen |
| P2 | Mitra | Informasi Dokumen KS | Data dokumen terkait mitra |

---

## 5. DFD Level 1 — Mengelola Data Master

Dekomposisi dari **P1. Mengelola Data Master**

### 5.1 Diagram

```mermaid
graph TB
    E1["🔧 Admin"]
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E8["🏭 Mitra"]

    P11(("1.1\nMengelola\nData\nPengguna"))
    P12(("1.2\nMengelola\nData Role"))
    P13(("1.3\nMengelola\nData Mitra"))
    P14(("1.4\nMengelola\nData Unit"))
    P15(("1.5\nMengelola\nData\nReferensi"))
    P16(("1.6\nMengirim\nAkses Login\nMitra"))

    D1[("D1 Data Pengguna")]
    D2[("D2 Data Mitra")]
    D9[("D9 Data Unit")]
    D10[("D10 Data Referensi")]

    E1 -- "Data Pengguna" --> P11
    P11 -- "Konfirmasi" --> E1
    P11 <--> D1

    E1 -- "Data Role" --> P12
    P12 -- "Konfirmasi" --> E1
    P12 <--> D1

    E1 -- "Data Mitra" --> P13
    E34 -- "Data Mitra" --> P13
    P13 -- "Konfirmasi" --> E1
    P13 -- "Konfirmasi" --> E34
    P13 <--> D2

    E1 -- "Data Unit" --> P14
    P14 -- "Konfirmasi" --> E1
    P14 <--> D9

    E1 -- "Data Klasifikasi,\nData Jenis KS" --> P15
    P15 -- "Konfirmasi" --> E1
    P15 <--> D10

    E1 -- "Perintah Kirim\nAkses Login" --> P16
    D2 -- "Data Mitra" --> P16
    P16 --> D1
    P16 -- "Credential Login" --> E8
```

### 5.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 1.1 | Mengelola Data Pengguna | Data Pengguna (Nama, NIK, Email, Password, Role) | Konfirmasi Penyimpanan | D1 (Read/Write) |
| 1.2 | Mengelola Data Role | Data Role (Nama Role, Permission) | Konfirmasi Penyimpanan | D1 (Read/Write) |
| 1.3 | Mengelola Data Mitra | Data Mitra (Nama, Alamat, Klasifikasi, Email, Telp) | Konfirmasi Penyimpanan | D2 (Read/Write) |
| 1.4 | Mengelola Data Unit | Data Unit (Nama, Kode, Ketua/Kepala) | Konfirmasi Penyimpanan | D9 (Read/Write) |
| 1.5 | Mengelola Data Referensi | Data Klasifikasi, Data Jenis KS, Data Sasaran | Konfirmasi Penyimpanan | D10 (Read/Write) |
| 1.6 | Mengirim Akses Login Mitra | Perintah Kirim, Data Mitra | Credential Login (Email ke Mitra) | D1 (Write), D2 (Read) |

---

## 6. DFD Level 1 — Mengelola Dokumen Kerja Sama

Dekomposisi dari **P2. Mengelola Dokumen Kerja Sama**

### 6.1 Diagram

```mermaid
graph TB
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E8["🏭 Mitra"]

    P21(("2.1\nMenginput\nDokumen KS"))
    P22(("2.2\nMemvalidasi\nData Dokumen"))
    P23(("2.3\nMenyimpan\nDokumen KS"))
    P24(("2.4\nMengedit\nDokumen KS"))
    P25(("2.5\nMensubmit\nDokumen ke\nPimpinan"))
    P26(("2.6\nMenampilkan\nDokumen KS\nMitra"))

    D2[("D2 Data Mitra")]
    D3[("D3 Data Dokumen KS")]
    D8[("D8 Data Notifikasi")]
    D9[("D9 Data Unit")]

    E34 -- "Data Dokumen KS:\nJudul, Nomor, Jenis,\nMitra, Tanggal, Ruang Lingkup,\nFile Dokumen" --> P21
    P21 -- "Data Dokumen" --> P22
    D2 -- "Data Mitra" --> P21
    D9 -- "Data Unit" --> P21

    P22 -- "Data Valid" --> P23
    P22 -- "Pesan Error\nValidasi" --> E34

    P23 --> D3
    P23 -- "Konfirmasi Status:\nDokumen Tersimpan (Draft)" --> E34

    E34 -- "Data Perubahan\nDokumen" --> P24
    D3 -- "Data Dokumen\nBerstatus Draft/Revisi" --> P24
    P24 -- "Data Dokumen\nTerubah" --> P22

    E34 -- "Perintah Submit" --> P25
    D3 -- "Data Dokumen\n(Draft)" --> P25
    P25 -- "Data Dokumen\n(Status: Menunggu Evaluasi)" --> D3
    P25 -- "Notifikasi\nSubmit" --> D8
    P25 -- "Konfirmasi:\nDokumen Telah Disubmit" --> E34

    E8 -- "Permintaan Lihat\nDokumen KS" --> P26
    D3 -- "Data Dokumen KS\nTerkait Mitra" --> P26
    P26 -- "Informasi Dokumen KS,\nFile Dokumen" --> E8
    E8 -- "Catatan Review" --> P26
    P26 -- "Data Review Mitra" --> D3
```

### 6.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 2.1 | Menginput Dokumen KS | Data Dokumen KS (Judul, Nomor, Jenis, Mitra, Tanggal, File) | Data Dokumen (ke validasi) | D2 (Read), D9 (Read) |
| 2.2 | Memvalidasi Data Dokumen | Data Dokumen | Data Valid / Pesan Error | — |
| 2.3 | Menyimpan Dokumen KS | Data Valid | Konfirmasi (Status Draft) | D3 (Write) |
| 2.4 | Mengedit Dokumen KS | Data Perubahan, Data Dokumen Lama | Data Terubah (ke validasi) | D3 (Read) |
| 2.5 | Mensubmit Dokumen ke Pimpinan | Perintah Submit, Data Dokumen Draft | Status: Menunggu Evaluasi | D3 (Write), D8 (Write) |
| 2.6 | Menampilkan Dokumen KS Mitra | Permintaan Lihat, Catatan Review | Informasi Dokumen, Data Review | D3 (Read/Write) |

---

## 7. DFD Level 1 — Mengelola Pengajuan Kerja Sama

Dekomposisi dari **P3. Mengelola Pengajuan Kerja Sama**

### 7.1 Diagram

```mermaid
graph TB
    E8["🏭 Mitra"]

    P31(("3.1\nMengisi\nForm\nPengajuan"))
    P32(("3.2\nMemvalidasi\nData\nPengajuan"))
    P33(("3.3\nMemeriksa\nDuplikasi\nEmail"))
    P34(("3.4\nMenyimpan\nPengajuan"))
    P35(("3.5\nMengirim\nNotifikasi\nke Pimpinan"))
    P36(("3.6\nMengajukan\nPerpanjangan\nKS"))

    D1[("D1 Data Pengguna")]
    D2[("D2 Data Mitra")]
    D3[("D3 Data Dokumen KS")]
    D8[("D8 Data Notifikasi")]

    E8 -- "Data Pengajuan KS:\nNama Instansi, Alamat,\nBidang KS, Ruang Lingkup,\nFile Proposal" --> P31
    
    P31 -- "Data Pengajuan" --> P32
    P32 -- "Pesan Error" --> E8
    P32 -- "Data Valid" --> P33
    
    D1 -- "Data Email\nTerdaftar" --> P33
    P33 -- "Peringatan:\nEmail Sudah Terdaftar" --> E8
    P33 -- "Data Pengajuan\n(Unik)" --> P34

    P34 --> D3
    D2 -- "Data Mitra" --> P34
    P34 -- "Konfirmasi Pengajuan\nBerhasil Dikirim" --> E8

    P34 -- "Data Pengajuan Baru" --> P35
    P35 --> D8

    E8 -- "Data Perpanjangan:\nJangka Waktu Baru,\nRuang Lingkup Baru,\nDokumen Pendukung" --> P36
    D3 -- "Data KS Lama" --> P36
    P36 -- "Data Perpanjangan" --> P32
```

### 7.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 3.1 | Mengisi Form Pengajuan | Data Pengajuan KS (Nama, Alamat, Bidang, Ruang Lingkup, Proposal) | Data Pengajuan (ke validasi) | — |
| 3.2 | Memvalidasi Data Pengajuan | Data Pengajuan | Data Valid / Pesan Error | — |
| 3.3 | Memeriksa Duplikasi Email | Data Pengajuan, Data Email Terdaftar | Pengajuan Unik / Peringatan Duplikat | D1 (Read) |
| 3.4 | Menyimpan Pengajuan | Data Pengajuan Unik | Konfirmasi Pengajuan | D3 (Write), D2 (Read) |
| 3.5 | Mengirim Notifikasi ke Pimpinan | Data Pengajuan Baru | Notifikasi | D8 (Write) |
| 3.6 | Mengajukan Perpanjangan KS | Data Perpanjangan, Data KS Lama | Data Perpanjangan (ke validasi) | D3 (Read) |

---

## 8. DFD Level 1 — Memvalidasi Dokumen dan Pengajuan

Dekomposisi dari **P4. Memvalidasi Dokumen dan Pengajuan**

### 8.1 Diagram

```mermaid
graph TB
    E2["👔 Pimpinan"]
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E8["🏭 Mitra"]

    P41(("4.1\nMenampilkan\nDaftar\nMenunggu\nValidasi"))
    P42(("4.2\nMemeriksa\nData\nDokumen"))
    P43(("4.3\nMenentukan\nKeputusan\nValidasi"))
    P44(("4.4\nMengesahkan\nDokumen KS"))
    P45(("4.5\nMemvalidasi\nPengajuan\nBaru"))
    P46(("4.6\nMembuat\nAkun Mitra\nOtomatis"))
    P47(("4.7\nMengirim\nNotifikasi\nHasil"))

    D1[("D1 Data Pengguna")]
    D3[("D3 Data Dokumen KS")]
    D8[("D8 Data Notifikasi")]

    D3 -- "Data Dokumen\nMenunggu Validasi" --> P41
    P41 -- "Daftar Dokumen\nMenunggu" --> E2

    E2 -- "Pilih Dokumen" --> P42
    D3 -- "Detail Dokumen" --> P42
    P42 -- "Data Lengkap\nDokumen" --> E2

    E2 -- "Keputusan:\nSetuju/Revisi" --> P43
    P43 -- "Status: Menunggu\nValidasi" --> D3
    P43 -- "Status: Revisi" --> D3
    P43 -- "Catatan Revisi" --> P47

    E2 -- "Keputusan:\nSahkan" --> P44
    P44 -- "Status: Disahkan,\nTanggal Pengesahan" --> D3
    P44 -- "Data Pengesahan" --> P47

    E2 -- "Keputusan Pengajuan:\nSetuju/Tolak" --> P45
    D3 -- "Data Pengajuan" --> P45
    P45 -- "Status Pengajuan" --> D3
    P45 -- "Data Mitra Disetujui" --> P46

    D1 -- "Cek Akun Mitra" --> P46
    P46 -- "Akun Mitra Baru" --> D1
    P46 -- "Credential Login" --> P47

    P47 -- "Notifikasi Revisi" --> E34
    P47 -- "Notifikasi Persetujuan,\nCredential Login" --> E8
    P47 --> D8
```

### 8.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 4.1 | Menampilkan Daftar Menunggu Validasi | Data Dokumen Menunggu | Daftar Dokumen Menunggu (ke Pimpinan) | D3 (Read) |
| 4.2 | Memeriksa Data Dokumen | Pilihan Dokumen | Data Lengkap Dokumen | D3 (Read) |
| 4.3 | Menentukan Keputusan Validasi | Keputusan Pimpinan (Setuju/Revisi) | Status Baru, Catatan | D3 (Write) |
| 4.4 | Mengesahkan Dokumen KS | Keputusan Sahkan | Status Disahkan, Tanggal Pengesahan | D3 (Write) |
| 4.5 | Memvalidasi Pengajuan Baru | Keputusan Setuju/Tolak, Data Pengajuan | Status Pengajuan, Data Mitra Disetujui | D3 (Read/Write) |
| 4.6 | Membuat Akun Mitra Otomatis | Data Mitra Disetujui, Cek Akun | Akun Mitra Baru, Credential | D1 (Read/Write) |
| 4.7 | Mengirim Notifikasi Hasil | Catatan, Data Pengesahan, Credential | Notifikasi ke Unit & Mitra | D8 (Write) |

---

## 9. DFD Level 1 — Mengelola Kegiatan dan Monitoring

Dekomposisi dari **P5. Mengelola Kegiatan dan Monitoring**

### 9.1 Diagram

```mermaid
graph TB
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E5["📚 Prodi"]
    E8["🏭 Mitra"]
    E2["👔 Pimpinan"]

    P51(("5.1\nMenginput\nKegiatan\nKerja Sama"))
    P52(("5.2\nMenginput\nPeserta\nMahasiswa"))
    P53(("5.3\nMemvalidasi\nData\nMahasiswa"))
    P54(("5.4\nMenyimpan\nPenempatan\nMahasiswa"))
    P55(("5.5\nMemberi\nPenilaian\nMahasiswa"))
    P56(("5.6\nMemonitoring\nMahasiswa\nAktif"))

    D2[("D2 Data Mitra")]
    D3[("D3 Data Dokumen KS")]
    D4[("D4 Data Kegiatan")]
    D5[("D5 Data Mahasiswa")]
    D8[("D8 Data Notifikasi")]
    D10[("D10 Data Referensi")]

    E34 -- "Data Kegiatan:\nNama, Jenis KS,\nSasaran, Indikator,\nPeriode, Volume" --> P51
    E5 -- "Data Kegiatan" --> P51
    D3 -- "Data Dokumen IA\n(Disahkan)" --> P51
    D10 -- "Data Jenis KS,\nSasaran, Indikator" --> P51
    D2 -- "Data Mitra\nPelaksana" --> P51
    P51 --> D4
    P51 -- "Konfirmasi\nKegiatan Tersimpan" --> E34
    P51 -- "Konfirmasi\nKegiatan Tersimpan" --> E5

    E5 -- "Data Mahasiswa:\nNIM, Nama, Prodi,\nAngkatan, Mitra\nPenempatan, Periode,\nPembimbing" --> P52
    P52 -- "Data Mahasiswa" --> P53
    D5 -- "Data MHS\nTerdaftar" --> P53
    P53 -- "Pesan Error:\nNIM Tidak Valid /\nSudah Terdaftar" --> E5
    P53 -- "Data Valid" --> P54

    P54 --> D5
    D2 -- "Data Mitra\nPenempatan" --> P54
    P54 -- "Konfirmasi\nPenempatan" --> E5
    P54 -- "Notifikasi\nPenempatan" --> D8

    E8 -- "Data Penilaian:\nSkor, Aspek Penilaian,\nCatatan" --> P55
    D5 -- "Data MHS\ndi Mitra" --> P55
    P55 -- "Nilai MHS" --> D5
    P55 -- "Notifikasi Penilaian" --> D8

    D4 -- "Data Kegiatan" --> P56
    D5 -- "Data Penempatan\nMHS" --> P56
    P56 -- "Data Monitoring:\nJumlah, Status,\nDistribusi, Nilai" --> E2
    P56 -- "Data Monitoring" --> E5
    P56 -- "Data Monitoring" --> E8
    P56 -- "Data Monitoring" --> E34
```

### 9.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 5.1 | Menginput Kegiatan KS | Data Kegiatan, Data IA, Data Referensi, Data Mitra | Konfirmasi Tersimpan | D4 (Write), D3 (Read), D10 (Read), D2 (Read) |
| 5.2 | Menginput Peserta Mahasiswa | Data Mahasiswa (NIM, Nama, Prodi, Mitra, Pembimbing) | Data Mahasiswa (ke validasi) | — |
| 5.3 | Memvalidasi Data Mahasiswa | Data Mahasiswa, Data MHS Terdaftar | Data Valid / Pesan Error | D5 (Read) |
| 5.4 | Menyimpan Penempatan Mahasiswa | Data Valid | Konfirmasi Penempatan | D5 (Write), D2 (Read), D8 (Write) |
| 5.5 | Memberi Penilaian Mahasiswa | Data Penilaian (Skor, Aspek, Catatan) | Nilai MHS Tersimpan | D5 (Read/Write), D8 (Write) |
| 5.6 | Memonitoring Mahasiswa Aktif | Data Kegiatan, Data Penempatan | Data Monitoring (Jumlah, Status, Distribusi) | D4 (Read), D5 (Read) |

---

## 10. DFD Level 1 — Mengelola Evaluasi

Dekomposisi dari **P6. Mengelola Evaluasi**

### 10.1 Diagram

```mermaid
graph TB
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E2["👔 Pimpinan"]
    E8["🏭 Mitra"]

    P61(("6.1\nMengisi\nForm\nEvaluasi"))
    P62(("6.2\nMemvalidasi\nData\nEvaluasi"))
    P63(("6.3\nMenyimpan\nEvaluasi"))
    P64(("6.4\nMensubmit\nEvaluasi ke\nPimpinan"))
    P65(("6.5\nMemvalidasi\nEvaluasi"))
    P66(("6.6\nMencatat\nUmpan Balik\nMitra"))

    D3[("D3 Data Dokumen KS")]
    D6[("D6 Data Evaluasi")]
    D8[("D8 Data Notifikasi")]

    E34 -- "Data Evaluasi:\nPeriode, Realisasi\nVolume/Output/Outcome,\nKendala, Rekomendasi,\nKesimpulan" --> P61
    D3 -- "Data KS Aktif" --> P61
    P61 -- "Data Evaluasi" --> P62
    P62 -- "Pesan Error" --> E34
    P62 -- "Data Valid" --> P63
    P63 --> D6
    P63 -- "Konfirmasi:\nEvaluasi Tersimpan (Draft)" --> E34

    E34 -- "Perintah Submit\nEvaluasi" --> P64
    D6 -- "Data Evaluasi\n(Draft)" --> P64
    P64 -- "Status: Menunggu\nValidasi" --> D6
    P64 -- "Notifikasi\nSubmit Evaluasi" --> D8
    P64 -- "Konfirmasi:\nEvaluasi Disubmit" --> E34

    E2 -- "Keputusan:\nSetujui/Revisi" --> P65
    D6 -- "Data Evaluasi\nMenunggu Validasi" --> P65
    P65 -- "Status:\nDisetujui/Revisi" --> D6
    P65 -- "Hasil Validasi\nEvaluasi" --> E2
    P65 -- "Notifikasi Hasil" --> D8
    P65 -- "Notifikasi Revisi\nEvaluasi" --> E34

    E8 -- "Data Umpan Balik:\nRating, Aspek Kepuasan,\nSaran, Kesediaan\nPerpanjangan" --> P66
    D3 -- "Data KS\nTerkait Mitra" --> P66
    P66 --> D6
    P66 -- "Konfirmasi:\nUmpan Balik Tersimpan" --> E8
    P66 -- "Notifikasi Umpan\nBalik Baru" --> D8
```

### 10.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 6.1 | Mengisi Form Evaluasi | Data Evaluasi (Periode, Realisasi, Kendala, Rekomendasi) | Data Evaluasi (ke validasi) | D3 (Read) |
| 6.2 | Memvalidasi Data Evaluasi | Data Evaluasi | Data Valid / Pesan Error | — |
| 6.3 | Menyimpan Evaluasi | Data Valid | Konfirmasi (Status Draft) | D6 (Write) |
| 6.4 | Mensubmit Evaluasi ke Pimpinan | Perintah Submit, Data Evaluasi Draft | Status: Menunggu Validasi | D6 (Write), D8 (Write) |
| 6.5 | Memvalidasi Evaluasi | Keputusan Pimpinan, Data Evaluasi | Status: Disetujui/Revisi | D6 (Read/Write), D8 (Write) |
| 6.6 | Mencatat Umpan Balik Mitra | Data Umpan Balik (Rating, Saran), Data KS | Konfirmasi Tersimpan | D6 (Write), D3 (Read), D8 (Write) |

---

## 11. DFD Level 1 — Mengelola Tracking Lulusan

Dekomposisi dari **P7. Mengelola Tracking Lulusan**

### 11.1 Diagram

```mermaid
graph TB
    E5["📚 Prodi"]
    E8["🏭 Mitra"]
    E2["👔 Pimpinan"]
    E4["🎓 Jurusan"]

    P71(("7.1\nMenginput\nData\nAlumni"))
    P72(("7.2\nMemvalidasi\nData Alumni"))
    P73(("7.3\nMenghubungkan\nAlumni\ndengan Mitra"))
    P74(("7.4\nMenghitung\nStatistik\nPenyerapan"))

    D2[("D2 Data Mitra")]
    D7[("D7 Data Alumni")]
    D9[("D9 Data Unit")]

    E5 -- "Data Alumni:\nNIM, Nama, Prodi,\nTahun Lulus, Email" --> P71
    E8 -- "Data Lulusan\ndi Mitra:\nNama, Posisi,\nTahun Mulai" --> P71
    P71 -- "Data Alumni" --> P72
    P72 -- "Pesan Error:\nNIM Tidak Valid /\nData Duplikat" --> E5
    P72 -- "Pesan Error" --> E8
    D7 -- "Data Alumni\nTerdaftar" --> P72
    P72 -- "Data Valid" --> P73

    P73 -- "Relasi Alumni-Mitra:\nAlumni ID, Mitra ID,\nPosisi, Tahun Mulai,\nStatus, Sumber Data" --> D7
    D2 -- "Data Mitra" --> P73
    P73 -- "Konfirmasi\nData Tersimpan" --> E5
    P73 -- "Konfirmasi\nData Tersimpan" --> E8

    D7 -- "Data Alumni\n& Relasi Mitra" --> P74
    D2 -- "Data Mitra" --> P74
    D9 -- "Data Prodi" --> P74
    P74 -- "Statistik Penyerapan:\nTotal Alumni, % Bekerja\ndi Mitra KS, Trend\nper Tahun, per Prodi" --> E5
    P74 -- "Statistik Penyerapan" --> E8
    P74 -- "Statistik Penyerapan" --> E2
    P74 -- "Statistik Penyerapan" --> E4
```

### 11.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 7.1 | Menginput Data Alumni | Data Alumni (NIM, Nama, Prodi, Tahun Lulus), Data Lulusan di Mitra | Data Alumni (ke validasi) | — |
| 7.2 | Memvalidasi Data Alumni | Data Alumni, Data Alumni Terdaftar | Data Valid / Pesan Error | D7 (Read) |
| 7.3 | Menghubungkan Alumni dengan Mitra | Data Valid | Relasi Alumni-Mitra | D7 (Write), D2 (Read) |
| 7.4 | Menghitung Statistik Penyerapan | Data Alumni, Data Mitra, Data Prodi | Statistik Penyerapan Lulusan | D7 (Read), D2 (Read), D9 (Read) |

---

## 12. DFD Level 1 — Membuat Laporan dan Dashboard

Dekomposisi dari **P8. Membuat Laporan dan Dashboard**

### 12.1 Diagram

```mermaid
graph TB
    E1["🔧 Admin"]
    E2["👔 Pimpinan"]
    E34["🏢 Humas / 🎓 Jurusan\n🏛 UPA / 🔬 Pusat"]
    E5["📚 Prodi"]
    E8["🏭 Mitra"]

    P81(("8.1\nMenampilkan\nDashboard\nEksekutif"))
    P82(("8.2\nMenampilkan\nDashboard\nPer-Unit"))
    P83(("8.3\nMenampilkan\nDashboard\nMitra"))
    P84(("8.4\nMenghasilkan\nLaporan\nPDF/Excel"))
    P85(("8.5\nMenampilkan\nAnalitik"))

    D2[("D2 Data Mitra")]
    D3[("D3 Data Dokumen KS")]
    D4[("D4 Data Kegiatan")]
    D5[("D5 Data Mahasiswa")]
    D6[("D6 Data Evaluasi")]
    D7[("D7 Data Alumni")]
    D8[("D8 Data Notifikasi")]

    D3 -- "Data KS" --> P81
    D4 -- "Data Kegiatan" --> P81
    D6 -- "Data Evaluasi" --> P81
    D2 -- "Data Mitra" --> P81
    D7 -- "Data Alumni" --> P81
    D8 -- "Data Notifikasi\nPending" --> P81
    P81 -- "Dashboard Eksekutif:\nKPI, Grafik, Peta,\nDaftar Pending" --> E2

    D3 -- "Data KS\nPer Unit" --> P82
    D4 -- "Data Kegiatan\nPer Unit" --> P82
    D6 -- "Data Evaluasi\nPer Unit" --> P82
    P82 -- "Dashboard Unit:\nStatistik, Grafik,\nDaftar Aktif" --> E34
    P82 -- "Dashboard Unit" --> E5

    D3 -- "Data KS\nPer Mitra" --> P83
    D5 -- "Data MHS\ndi Mitra" --> P83
    P83 -- "Dashboard Mitra:\nRingkasan KS,\nMHS Aktif, Quick Action" --> E8

    E2 -- "Parameter Laporan:\nJenis, Periode,\nUnit, Format" --> P84
    E34 -- "Parameter Laporan" --> P84
    E1 -- "Parameter Laporan" --> P84
    D3 -- "Data KS" --> P84
    D4 -- "Data Kegiatan" --> P84
    D5 -- "Data MHS" --> P84
    D6 -- "Data Evaluasi" --> P84
    D7 -- "Data Alumni" --> P84
    P84 -- "File Laporan\n(PDF/Excel)" --> E2
    P84 -- "File Laporan\n(PDF/Excel)" --> E34
    P84 -- "File Laporan\n(PDF/Excel)" --> E1

    D3 -- "Data KS" --> P85
    D2 -- "Data Mitra" --> P85
    D4 -- "Data Kegiatan" --> P85
    P85 -- "Analitik:\nStatus, Geografis,\nKlasifikasi, Kegiatan" --> E2
    P85 -- "Analitik" --> E34
    P85 -- "Analitik" --> E1
```

### 12.2 Deskripsi Proses

| Kode | Proses | Input | Output | Data Store |
|------|--------|-------|--------|------------|
| 8.1 | Menampilkan Dashboard Eksekutif | Data KS, Data Kegiatan, Data Evaluasi, Data Mitra, Data Alumni, Notifikasi Pending | Dashboard Eksekutif (KPI, Grafik, Peta, Daftar Pending) | D3, D4, D6, D2, D7, D8 (Read) |
| 8.2 | Menampilkan Dashboard Per-Unit | Data KS/Kegiatan/Evaluasi per Unit | Dashboard Unit (Statistik, Grafik, Daftar Aktif) | D3, D4, D6 (Read) |
| 8.3 | Menampilkan Dashboard Mitra | Data KS per Mitra, Data MHS di Mitra | Dashboard Mitra (Ringkasan, MHS Aktif, Quick Action) | D3, D5 (Read) |
| 8.4 | Menghasilkan Laporan PDF/Excel | Parameter Laporan, Data dari Multiple Store | File Laporan (PDF/Excel) | D3, D4, D5, D6, D7 (Read) |
| 8.5 | Menampilkan Analitik | Data KS, Data Mitra, Data Kegiatan | Visualisasi Analitik (Status, Geo, Klasifikasi) | D3, D2, D4 (Read) |

---

## 13. Ringkasan Balancing DFD

Tabel berikut menunjukkan konsistensi aliran data dari Context Diagram ke DFD Level 0 dan Level 1.

### 13.1 Balancing Context Diagram ↔ DFD Level 0

| External Entity | Data Flow (Context) | Proses Level 0 yang Menerima/Mengirim |
|-----------------|--------------------|------------------------------------|
| Admin | Data Pengguna, Data Role, Data Klasifikasi, Data Unit → | P1 (Mengelola Data Master) |
| Admin | Credential Mitra → | P1 (Mengelola Data Master) |
| Admin | ← Konfirmasi Penyimpanan, Notifikasi, Laporan | P1, P8 |
| Pimpinan | Data Validasi, Keputusan → | P4 (Memvalidasi Dokumen & Pengajuan) |
| Pimpinan | Data Evaluasi → | P6 (Mengelola Evaluasi) |
| Pimpinan | ← Laporan Eksekutif, Notifikasi, Statistik, Daftar Menunggu | P4, P5, P7, P8 |
| Humas/Jurusan/UPA/Pusat | Data Dokumen KS, Data Mitra → | P2 (Mengelola Dokumen KS), P1 |
| Humas/Jurusan/UPA/Pusat | Data Kegiatan → | P5 (Mengelola Kegiatan) |
| Humas/Jurusan/UPA/Pusat | Data Evaluasi → | P6 (Mengelola Evaluasi) |
| Humas/Jurusan/UPA/Pusat | ← Konfirmasi Status, Notifikasi, Laporan Unit | P2, P4, P6, P8 |
| Prodi | Data Kegiatan, Data Mahasiswa → | P5 (Mengelola Kegiatan) |
| Prodi | Data Lulusan → | P7 (Mengelola Tracking Lulusan) |
| Prodi | ← Konfirmasi Penempatan, Notifikasi, Statistik Lulusan | P5, P7, P8 |
| Mitra | Data Pengajuan KS, Data Perpanjangan → | P3 (Mengelola Pengajuan KS) |
| Mitra | Data Penilaian MHS → | P5 (Mengelola Kegiatan) |
| Mitra | Data Lulusan, Data Umpan Balik → | P6, P7 |
| Mitra | ← Informasi Dokumen KS, Notifikasi, Credential, Statistik | P1, P2, P4, P5, P7, P8 |

### 13.2 Balancing DFD Level 0 ↔ DFD Level 1

| Proses Level 0 | Sub-Proses Level 1 | Data Flow Eksternal Konsisten? |
|----------------|--------------------|-----------------------------|
| P1 Mengelola Data Master | 1.1–1.6 | ✅ Ya |
| P2 Mengelola Dokumen KS | 2.1–2.6 | ✅ Ya |
| P3 Mengelola Pengajuan KS | 3.1–3.6 | ✅ Ya |
| P4 Memvalidasi Dokumen & Pengajuan | 4.1–4.7 | ✅ Ya |
| P5 Mengelola Kegiatan & Monitoring | 5.1–5.6 | ✅ Ya |
| P6 Mengelola Evaluasi | 6.1–6.6 | ✅ Ya |
| P7 Mengelola Tracking Lulusan | 7.1–7.4 | ✅ Ya |
| P8 Membuat Laporan & Dashboard | 8.1–8.5 | ✅ Ya |

---

## 14. Checklist Validasi DFD

### 14.1 Context Diagram

- [x] Hanya memiliki satu proses utama (Sistem Informasi Kerja Sama WD4)
- [x] Semua External Entity teridentifikasi (8 entitas)
- [x] Semua data flow masuk (input) tergambar
- [x] Semua data flow keluar (output) tergambar
- [x] Tidak memiliki Data Store
- [x] Tidak memiliki proses internal

### 14.2 DFD Level 0

- [x] Semua proses utama teridentifikasi (8 proses)
- [x] External Entity konsisten dengan Context Diagram
- [x] Data Flow eksternal konsisten dengan Context Diagram
- [x] Data Store digunakan secara logis (10 data store)
- [x] Semua proses memiliki input dan output
- [x] Tidak ada proses Black Hole (tanpa output)
- [x] Tidak ada proses Miracle (tanpa input)
- [x] External Entity tidak langsung terhubung ke Data Store

### 14.3 DFD Level 1

- [x] Proses merupakan hasil dekomposisi dari Level 0
- [x] Penomoran konsisten (1.1, 1.2, ... 8.1, 8.2, dst.)
- [x] External Entity tetap konsisten
- [x] Data Flow eksternal tetap konsisten (balancing)
- [x] Data Store digunakan secara logis
- [x] Tidak ada aliran data yang tidak jelas
- [x] Setiap proses memiliki transformasi data yang jelas

### 14.4 Visual

- [x] Arah aliran data jelas
- [x] Nama menggunakan konvensi yang benar (kata kerja untuk proses, kata benda untuk entity/data flow/data store)
- [x] Penomoran mudah dipahami
- [x] Diagram dapat dipahami tanpa penjelasan lisan

---

> [!NOTE]
> **Ringkasan DFD**:
> - **Context Diagram**: 1 proses utama, 8 external entity
> - **DFD Level 0**: 8 proses utama, 10 data store
> - **DFD Level 1**: 42 sub-proses (dekomposisi dari 8 proses utama)
> - **Total Data Store**: 10 penyimpanan data

> [!IMPORTANT]
> Dokumen ini menggambarkan aliran data sistem secara **konseptual**. Setiap data store berkorespondensi dengan tabel database yang tercantum di dokumen [planning.md](file:///c:/laragon/www/wd4/pengembangan-sistem/planning.md). Untuk detail struktur data, lihat ERD pada bagian rancangan database.

> [!TIP]
> Untuk traceability, kode proses DFD (P1–P8 dan sub-proses 1.1–8.5) dapat direferensikan silang dengan kode use case (UC01–UC37) dari dokumen [analysis-use-case.md](file:///c:/laragon/www/wd4/pengembangan-sistem/analysis-use-case.md) untuk memastikan konsistensi antar diagram.
