# Rencana Pengujian & Matriks Data Uji (Test Plan)
## Form Tambah Kerja Sama — Role Humas / Unit (`create_kerjasama.blade.php`)

Dokumen ini berisi panduan skenario pengujian fungsional, validasi batas karakter (*boundary test*), serta data uji siap pakai (*ready-to-use dummy data*) untuk memverifikasi halaman pembuatan kerja sama pada tingkat Institusi / Humas Politeknik Negeri Manado.

---

## 📋 1. Ringkasan Fitur & Informasi Pengujian

| Parameter | Keterangan |
| :--- | :--- |
| **Target View** | `resources/views/auth/layout/unit/create_kerjasama.blade.php` |
| **URL Route** | `/unit/data-kerjasama/create` |
| **HTTP Action** | `POST` ke route `unit.kerjasama.store` |
| **Aktor Penguji** | Pengguna internal dengan role `unit_kerja` (Humas) |
| **Karakteristik** | Form interaktif Alpine.js, live character counter, dynamic multiple mitra, dynamic pelaksana (Jurusan/UPA/Pusat), dynamic subform bentuk kegiatan |

---

## 🧪 2. Matriks Kasus Uji (Test Cases)

| Kode TC | Skenario Pengujian | Jenis Pengujian | Target Output |
| :--- | :--- | :--- | :--- |
| **TC-01** | Input Naskah MoU Standar Institusi | Positive Test | Dokumen tersimpan sebagai Draft, relasi mitra & pejabat valid |
| **TC-02** | Input Naskah MoA Multi-Pelaksana | Positive Test | Tersimpan dengan relasi multi-pelaksana (Jurusan + Prodi + UPA) |
| **TC-03** | Input Naskah IA Multi-Mitra (Konsorsium) | Positive Test | Fitur tambah penggiat dinamis & multiple PKS tersimpan rapi |
| **TC-04** | Uji Batas Karakter (*Boundary Limits*) | Boundary Test | Counter berubah warna (`warning` >85%, `danger` 100%), input terkunci pada `maxlength` |
| **TC-05** | Validasi Field Wajib (*Mandatory Fields*) | Negative Test | Form menolak submit jika field wajib kosong & menampilkan pesan error yang tepat |

---

## 📑 3. Dataset Testing Siap Pakai (Ready-to-Use Test Data)

### 🔹 TC-01: Input Naskah MoU Standar (Tingkat Institusi)
> **Skenario**: Penginputan dokumen payung kerja sama (MoU) 5 tahun dengan 1 mitra industri terdaftar.

| Bagian Formulir | Nama Field / Input | Nilai Data Uji (Test Data) |
| :--- | :--- | :--- |
| **Masa Berlaku** | Status Kerjasama | `Aktif` |
| | Tanggal Mulai | `01/10/2026` |
| | Tanggal Berakhir | `01/10/2031` |
| **Dokumentasi** | Link Google Drive | `https://drive.google.com/drive/folders/1abcXYZ-mou-polimdo-astra-2026` |
| **Dokumen Utama** | Jenis Dokumen | `MoU (Memorandum of Understanding)` |
| | Nomor Dokumen | `045/PL12/MOU/2026` |
| | Nomor PKS | *(Dikosongkan)* |
| | Judul Kerja Sama | `MoU Pengembangan SDM dan Penyerapan Lulusan Vokasi bersama PT Astra International` |
| | Deskripsi | `Kerja sama payung tingkat institusi dalam bidang pemagangan, kuliah tamu, dan kurikulum industri.` |
| **Pihak Ke-1 (Polimdo)** | Penandatangan | Nama: `Dr. Dra. Maryke Alelo, MBA`<br>Jabatan: `Direktur Politeknik Negeri Manado` |
| | Penanggung Jawab | Nama: `Stefi Tambingon, S.ST., M.T.`<br>Jabatan: `Kepala Kantor Urusan Internasional & Kerjasama` |
| **Pihak Ke-2 (Mitra)** | Nama Mitra | *Pilih Mitra Terdaftar:* `PT Astra International Tbk` |
| | Penandatangan | Nama: `Budi Hartono, M.M.`<br>Jabatan: `Head of Human Capital Development` |
| | Penanggung Jawab | Nama: `Siti Rahmawati`<br>Jabatan: `Talent Acquisition Specialist` |
| **Bentuk Kegiatan** | Pilihan Jenis | *Pilih:* `Perekrutan / Penyerapan Lulusan` |
| | Nilai Kontrak | `Rp 0` |
| | Income | `Non-finansial (Program rekrutmen lulusan)` |
| | Luaran | Volume: `50` \| Satuan: `Alumni` |
| | Output | `Terserapnya lulusan Politeknik Negeri Manado di divisi manufaktur dan teknologi informasi.` |
| | Outcome | `Peningkatan persentase ketercapaian IKU 1 (Lulusan mendapat pekerjaan yang layak).` |
| | Keterangan | `Program rekrutmen kampus tahunan berkala.` |
| | Tujuan | `Membuka jalur karir langsung bagi mahasiswa tingkat akhir dan alumni.` |
| | Sasaran & Indikator | *Pilih Sasaran IKU 1 & Indikator Lulusan yang sesuai* |

---

### 🔹 TC-02: Input Naskah MoA Multi-Pelaksana (Jurusan & UPA)
> **Skenario**: Penginputan perjanjian kerja sama teknis (MoA) yang melibatkan Jurusan Teknik Elektro (Prodi TI) dan UPA TIK.

| Bagian Formulir | Nama Field / Input | Nilai Data Uji (Test Data) |
| :--- | :--- | :--- |
| **Masa Berlaku** | Status & Periode | `Aktif` \| Mulai: `15/10/2026` \| Berakhir: `15/10/2028` |
| **Dokumen Utama** | Jenis Dokumen | `MoA (Memorandum of Agreement)` |
| | Nomor Dokumen | `089/PL12.TI/MOA/2026` |
| | Nomor PKS | `PKS-01/TELKOM-PL12/X/2026` |
| | Judul Kerja Sama | `MoA Penyelenggaraan Laboratorium Bersama dan Uji Kompetensi Cloud Computing` |
| | Deskripsi | `Implementasi teknis pendirian laboratorium komputasi awan dan sertifikasi mahasiswa.` |
| **Pelaksana Unit** | Tipe Pelaksana | Centang `Jurusan` dan `UPA` |
| | Pilihan Jurusan | `Teknik Elektro` ➔ Prodi: `D4 Teknik Informatika`, `D3 Manajemen Informatika` |
| | Pilihan UPA | `UPA Komputer dan TIK` |
| **Pihak Ke-1** | Penandatangan | Nama: `Anthonius V. S.Eng, M.T.`<br>Jabatan: `Ketua Jurusan Teknik Elektro` |
| **Pihak Ke-2** | Nama Mitra | `PT Telekomunikasi Indonesia Tbk` |
| | Penandatangan | Nama: `Ir. Hendra Gunawan`<br>Jabatan: `General Manager Enterprise Regional` |
| **Bentuk Kegiatan** | Pilihan Jenis | `Penyelenggaraan Uji Kompetensi` |
| | Nilai Kontrak | `Rp 150.000.000` |
| | Income | `Biaya sharing lisensi dan sertifikasi kompetensi global` |
| | Luaran | Volume: `120` \| Satuan: `Mahasiswa Bersertifikat` |

---

### 🔹 TC-03: Input Naskah IA Multi-Mitra (Konsorsium Perbankan)
> **Skenario**: Penginputan naskah pelaksanaan (IA) dengan lebih dari satu mitra (menggunakan tombol *Tambah Penggiat*) dan multi nomor PKS.

| Bagian Formulir | Nama Field / Input | Nilai Data Uji (Test Data) |
| :--- | :--- | :--- |
| **Dokumen Utama** | Jenis Dokumen | `IA (Implementation Agreement)` |
| | Nomor Dokumen | `12/PL12.HM/IA/2026` |
| | Nomor PKS | *PKS 1:* `PKS-BANK-SULUT-01`<br>*PKS 2:* `PKS-BANK-MANDIRI-02` *(Klik Tambah PKS)* |
| | Judul Kerja Sama | `IA Konsorsium Pembayaran UKT Host-to-Host dan Beasiswa Prestasi Mahasiswa` |
| **Pihak Ke-2** | Penggiat 1 (Mitra A) | Mitra: `PT Bank Pembangunan Daerah SulutGo`<br>TTD: `Johan Mantiri (Pimpinan Cabang)` |
| | Penggiat 2 (Mitra B) | *Klik Tambah Penggiat*<br>Mitra: `PT Bank Mandiri (Persero) Tbk`<br>TTD: `Reza Pratama (Area Manager)` |

---

### 🔹 TC-04: Boundary Testing (Uji Batas Karakter & Counter)
> **Skenario**: Memverifikasi ketepatan batas input teks, warna indikator counter, dan pencegahan *database truncation error*.

| Field | Batas Maks | Nilai Pengujian | Hasil UI & Sistem yang Diharapkan |
| :--- | :--- | :--- | :--- |
| **`title`** | 255 Karakter | Masukkan teks 218 karakter (85%) ➔ lalu 255 karakter | Counter: `218/255` (.warning / oranye), `255/255` (.danger / merah), input terkunci pada karakter ke-255. |
| **`doc_number`** | 255 Karakter | Masukkan nomor dokumen 255 karakter | Counter: `255/255`, visual rapi di dalam input grid. |
| **`pks_numbers[]`** | 255 Karakter | Masukkan teks nomor PKS 255 karakter | Counter per baris PKS: `255/255`. |
| **`description`** | 10.000 Karakter | Masukkan deskripsi panjang >8.500 karakter | Counter: `8501/10.000` (warna oranye), text tidak bisa melebihi 10.000. |
| **`income`** | 255 Karakter | Masukkan penjelasan pendapatan 255 karakter | Counter: `255/255`, textarea berhenti menerima input. |
| **`output` & `outcome`**| 10.000 Karakter | Masukkan penjelasan hasil dan dampak detail | Counter responsif `x/10.000` real-time. |

---

### 🔹 TC-05: Negative Testing (Validasi Form Kosong)
> **Skenario**: Memastikan sistem menolak penyimpanan data jika field wajib tidak diisi.

| Kasus Uji | Langkah Pengujian | Nilai Kosong | Ekspektasi Hasil |
| :--- | :--- | :--- | :--- |
| **Judul Kosong** | Kosongkan field Judul Kerjasama | `title = ""` | Browser/form memunculkan tooltip validasi *"Wajib diisi"*, data tidak terkirim. |
| **Mitra Kosong** | Tidak memilih mitra pada Pihak Ke-2 | `penggiat_mitra_ids[] = []` | Notifikasi validasi error: *"Pilih mitra kerja sama terlebih dahulu"*. |
| **Bentuk Kegiatan Kosong** | Tidak mencentang jenis kegiatan apapun | `jenis_kerjasama_detail_ids[] = []` | Notifikasi validasi error: *"Minimal pilih 1 jenis kegiatan"*. |

---

## 🏁 4. Kriteria Keberhasilan (Pass / Fail Criteria)

- ✅ **PASS (LULUS)**:
  1. Data tersimpan ke tabel `cooperations` dengan `status_dokumen = 'Draft'` dan `tingkat = 'Institusi'`.
  2. Semua relasi ke tabel `mitras`, `pejabat_internal`, `pejabat_mitra`, `jurusans`, `upas`, `pusats`, dan `cooperation_details` tersimpan utuh.
  3. Tidak terjadi error *500 Internal Server Error* atau *SQL Data truncation*.
  4. Muncul flash message sukses dan halaman teralihkan ke `/unit/data-kerjasama`.

- ❌ **FAIL (GAGAL)**:
  1. Terjadi pemotongan teks atau error database.
  2. Data mitra atau pelaksana tidak tersimpan ke tabel relasi.
  3. Form berhasil tersimpan saat field wajib dibiarkan kosong.
