# Arsitektur Komprehensif Sistem Informasi Kerja Sama & Kurikulum Link and Match
*(Integrasi Aturan Kampus Merdeka, Sinkronisasi Industri, dan Pengelolaan Dokumen Terpadu)*

Dokumen ini merupakan panduan lengkap dan terstruktur yang menggabungkan aturan kerja sama dari Direktorat Jenderal Pendidikan Tinggi dengan alur proses sistem informasi. Panduan ini dirancang untuk memastikan tata kelola dokumen legal, pelaksanaan akademik, dan penyelarasan kurikulum berjalan sinkron

---

## 1. Konsep Utama: Integrasi Kolaborasi dan Kurikulum
Sistem ini dirancang untuk mewadahi dua kepentingan utama:
1.  **Pelaporan Kinerja (Kampus Merdeka)**: Mengukur capaian Indikator Kinerja Utama (IKU) melalui kolaborasi dengan kawasan industri, memastikan mahasiswa mendapat pengalaman nyata di luar kampus.
2.  **Kurikulum *Link and Match***: Mendukung program bantuan penyusunan kurikulum yang adaptif, di mana kampus dan pihak industri bersama-sama merancang silabus, bahan ajar, dan metode evaluasi yang relevan dengan kebutuhan dunia kerja sesungguhnya.

## 2. Latar Belakang & Aturan Dasar Kerja Sama
Penyelenggaraan kerja sama perguruan tinggi bertujuan untuk memastikan mahasiswa mendapat pengalaman nyata dan kurikulum yang relevan dengan industri.
* **Kewajiban Legal**: Setiap bentuk kolaborasi, khususnya magang atau praktik kerja, wajib diikat dengan kesepakatan tertulis berupa MoU (Tingkat Institusi) atau Surat Perjanjian Kerja Sama / SPK (Tingkat Fakultas/Jurusan) [cite: 1]. Dokumen ini harus mengatur proses pembelajaran, pedoman SKS, dan rubrik penilaian [cite: 1].
* **Indikator Kinerja**: Kolaborasi dengan DUDIKA menjadi salah satu Indikator Kinerja Utama (IKU) yang dievaluasi secara nasional [cite: 1]

---

## 3. Struktur Pengguna & Hak Akses (Role-Based Access Control)
Manajemen hak akses harus dikelola dengan *middleware* yang ketat (misalnya dapat diinisialisasi melalui *seeder* di *database*) untuk memisahkan ranah administratif dan pengambilan keputusan:

* **Superadmin (Admin Institusi)**: Mengelola pengaturan inti sistem dan *master data* (daftar fakultas, unit, mitra DUDIKA) [cite: 5].
* **Pimpinan (Rektorat / Dekanat / Direktur)**: Memiliki wewenang eksklusif untuk **melakukan evaluasi akhir** [cite: 5]. Pimpinan bertugas menyetujui (*approve*), mengembalikan untuk revisi, atau menolak dokumen kerja sama dan proposal *Link and Match* [cite: 5].
* **Entitas Penginput Data (Humas, Jurusan, Pusat, dan UPA)**: Entitas ini berfungsi sebagai inisiator administratif yang **hanya menginput data** [cite: 5]. Mereka bertugas mengunggah draf usulan kerja sama, detail mitra, dan dokumen perjanjian sesuai cakupan unit masing-masing [cite: 5]. Mereka tidak memiliki akses untuk menyetujui dokumen.
* **Program Studi (Prodi)**: Berperan sebagai pelaksana teknis akademik di bawah Jurusan [cite: 5]. Tugas utamanya meliputi penyusunan kurikulum *Link and Match* berbasis Capaian Pembelajaran Lulusan (CPL), implementasi penempatan mahasiswa, konversi nilai SKS, serta menginput *Implementation Arrangement* (IA) [cite: 5].
* **Mitra (DUDIKA)**: Pihak industri yang mereviu draf kurikulum, menilai kinerja mahasiswa magang melalui rubrik, serta memonitor status dokumen perjanjian [cite: 5].

---

## 3. Entitas Data Utama (Database Architecture)
Untuk membangun relasi yang solid (menggunakan *Relational Database*), sistem membutuhkan pemetaan entitas berikut:

### A. Modul Sinkronisasi Kurikulum (Link and Match)
*   **Entitas Proposal**: Merekam pengajuan program bantuan kurikulum yang diusulkan oleh jurusan.
*   **Entitas Pemetaan Mata Kuliah**: Menghubungkan capaian pembelajaran lulusan (CPL) dari program studi dengan standar kompetensi dari DUDIKA.

### B. Modul Perjanjian & Status Kerja Sama
*   **Hierarki Dokumen**: MoU (Tingkat Institusi) -> MoA/SPK (Tingkat Fakultas/Jurusan) -> IA/Implementation Arrangement (Tingkat Teknis).
*   **Manajemen Status**: Melacak masa berlaku perjanjian (*Aktif*, *Kadaluarsa*, *Dalam Perpanjangan*, atau *Lain-lain*). 

### C. Modul Implementasi Kegiatan (8 Program Kampus Merdeka)
Pendataan teknis pelaksanaan kegiatan lapangan:
1.  Magang/Praktik Kerja (dengan rubrik pengakuan SKS).
2.  Studi/Proyek Independen.
3.  Kegiatan Wirausaha (didukung inkubator bisnis).
4.  Penelitian/Riset Kolaborasi.
5.  Membangun Desa (KKNT).
6.  Proyek Kemanusiaan.
7.  Asistensi Mengajar.
8.  Pertukaran Pelajar.

---

## 4. Legitimasi dan Pengakuan SKS
Sistem harus memiliki fitur "Konversi SKS" yang memungkinkan dokumen kerja sama (SPK) dihubungkan langsung dengan pedoman penilaian. Setiap mahasiswa yang terdaftar di dalam program kerja sama (terutama magang atau studi independen) akan dievaluasi menggunakan rubrik yang disepakati bersama oleh DUDIKA dan Perguruan Tinggi, kemudian nilainya dikonversi ke dalam sistem akademik kampus.

## 5. Fitur & Modul Utama Sistem

### A. Modul Manajemen Dokumen Kerja Sama
* **Formulir Pendataan**: Fitur bagi Jurusan, Humas, Pusat, dan UPA untuk merekam detail partner DUDIKA dan mengunggah draf dokumen [cite: 1, 5].
* **Sistem Pemantauan Status Dokumen**: Fitur pelacakan otomatis untuk status perjanjian yang diklasifikasikan menjadi: *Aktif* (dalam masa pelaksanaan), *Kadaluarsa* (habis masa berlaku), *Dalam Perpanjangan* (habis namun sedang diproses), dan *Lain-lain* [cite: 1].

### B. Modul Penyelarasan Kurikulum (Link and Match)
* **Pengajuan Proposal Kurikulum**: Fasilitas bagi Program Studi untuk mengajukan proposal penyusunan kurikulum bersama industri [cite: 5].
* **Matriks Pemetaan Kompetensi**: Ruang kerja digital untuk menyinkronkan standar DUDIKA dengan mata kuliah di Program Studi [cite: 5].

### C. Modul Pelaksanaan Kampus Merdeka
* **Pendaftaran 8 Bentuk Kegiatan**: Pencatatan mahasiswa pada program: (1) Pertukaran Pelajar, (2) Magang/Praktik Kerja, (3) Asistensi Mengajar, (4) Penelitian/Riset, (5) Proyek Kemanusiaan, (6) Kegiatan Wirausaha, (7) Studi Independen, (8) Membangun Desa (KKNT) [cite: 1].

### D. Modul Evaluasi & Konversi SKS
* **Portal Penilaian Mitra**: Antarmuka bagi DUDIKA untuk memberikan skor evaluasi kinerja mahasiswa di lapangan [cite: 5].
* **Kalkulator Konversi**: Fitur bagi Prodi untuk menerjemahkan nilai dari industri ke dalam transkrip SKS akademik [cite: 5].

## 4. Alur Bisnis Proses (Workflow) Kerja Sama
1. **Inisiasi & Input Dokumen**: Humas, Jurusan, Pusat, atau UPA memulai proses dengan menginput data usulan kerja sama dan draf dokumen (MoU/MoA) ke dalam sistem [cite: 5].
2. **Review Industri (Opsional)**: Mitra DUDIKA meninjau draf kesepakatan dan memberikan persetujuan atau catatan revisi [cite: 5].
3. **Validasi Akhir**: Pimpinan memeriksa usulan kerja sama tersebut. Jika sesuai, pimpinan memberikan evaluasi akhir berupa status disetujui (*Approved*) [cite: 5].
4. **Tindak Lanjut Akademik (Prodi)**: Berbekal dokumen yang telah disahkan, Program Studi menginput proposal *Link and Match*, merumuskan kurikulum bersama mitra, dan mendelegasikan mahasiswa untuk terjun ke lapangan [cite: 5].
5. **Pelaksanaan & Penilaian**: Mahasiswa menjalankan kegiatan. Mitra DUDIKA memberikan penilaian melalui rubrik yang tersedia di sistem [cite: 5].
6. **Konversi & Pelaporan**: Program Studi mengonversi nilai menjadi SKS [cite: 5]. Sistem secara otomatis menghasilkan rekapitulasi data untuk kebutuhan akreditasi dan pelaporan nasional ke kementerian [cite: 1, 5].