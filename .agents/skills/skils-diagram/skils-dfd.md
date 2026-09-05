# SKILLS DFD

## Panduan Standar Membuat Data Flow Diagram yang Terstruktur, Rapi, Konsisten, dan Mudah Dipahami

---

# 1. TUJUAN DOKUMEN

Dokumen ini menjadi panduan dalam membuat **Data Flow Diagram (DFD)** untuk kebutuhan:

* Analisis sistem.
* Perancangan sistem informasi.
* Dokumentasi sistem.
* Analisis aliran data.
* Identifikasi proses bisnis.
* Identifikasi sumber dan tujuan data.
* Identifikasi penyimpanan data.
* Dokumentasi sistem berjalan.
* Dokumentasi sistem yang akan dikembangkan.

Tujuan utama pembuatan DFD adalah memberikan gambaran mengenai:

> **Bagaimana data masuk ke dalam sistem, diproses oleh sistem, disimpan, dan dikeluarkan kembali sebagai informasi.**

DFD berfokus pada **aliran data**, bukan pada tampilan antarmuka, kode program, struktur class, atau urutan waktu secara detail.

---

# 2. KONSEP DASAR DFD

Data Flow Diagram atau DFD adalah diagram yang digunakan untuk menggambarkan:

* Sumber data.
* Tujuan data.
* Proses pengolahan data.
* Aliran data.
* Penyimpanan data.

Secara sederhana:

```text
External Entity
      ↓
Data Flow
      ↓
Process
      ↓
Data Store
      ↓
Process
      ↓
Data Flow
      ↓
External Entity
```

Contoh:

```text
Pelanggan
    |
    | Data Pesanan
    ↓
(1. Memproses Pesanan)
    |
    | Data Pesanan
    ↓
D1 Data Pesanan
```

DFD dapat digunakan untuk memodelkan:

```text
Sistem Berjalan
        atau
Sistem yang Akan Dikembangkan
```

DFD sangat cocok digunakan pada pendekatan **structured analysis / structured design**.

Jika sistem dirancang dengan pendekatan **Object-Oriented**, DFD dapat dilengkapi atau digantikan oleh diagram UML yang sesuai, seperti:

* Use Case Diagram.
* Activity Diagram.
* Sequence Diagram.
* Class Diagram.

---

# 3. FOKUS UTAMA DFD

DFD harus berfokus pada pertanyaan:

```text
1. Dari mana data berasal?
2. Ke mana data pergi?
3. Data apa yang mengalir?
4. Proses apa yang mengolah data?
5. Di mana data disimpan?
6. Proses apa yang membaca data?
7. Proses apa yang mengubah data?
```

Jangan menggunakan DFD untuk menjelaskan:

* Tampilan halaman website.
* Posisi tombol.
* Warna UI.
* Struktur HTML.
* Detail kode program.
* Detail algoritma.
* Urutan waktu yang sangat spesifik.
* Struktur class OOP.

Untuk kebutuhan tersebut gunakan diagram atau dokumentasi lain.

---

# 4. KOMPONEN DFD

DFD memiliki empat komponen utama:

```text
External Entity
Data Flow
Process
Data Store
```

---

# 5. EXTERNAL ENTITY

## 5.1. Pengertian

External Entity adalah pihak di luar sistem yang:

* Memberikan data kepada sistem.
* Menerima data dari sistem.
* Berinteraksi dengan sistem.

External Entity dapat berupa:

* Manusia.
* Role pengguna.
* Organisasi.
* Departemen.
* Sistem lain.
* Aplikasi eksternal.

Contoh:

```text
Pelanggan
Admin
Pimpinan
Jurusan
Unit Kerja
Mitra
Bank
Sistem Pembayaran
```

---

## 5.2. Aturan Penamaan

External Entity harus menggunakan **kata benda**.

Contoh yang benar:

```text
Pelanggan
Admin
Pimpinan
Mitra
Sistem Pembayaran
```

Contoh yang kurang tepat:

```text
Memesan Makanan
Memvalidasi Pengajuan
Membuat Laporan
```

Karena kata-kata tersebut merupakan aktivitas atau proses.

---

## 5.3. Aturan Utama

External Entity:

* Berada di luar batas sistem.
* Tidak memproses data secara internal.
* Hanya mengirim atau menerima data.
* Harus memiliki hubungan data flow dengan sistem.

Jangan menggambarkan External Entity langsung berhubungan dengan Data Store.

Contoh yang salah:

```text
Pelanggan
    |
    ↓
D1 Data Pesanan
```

Yang benar:

```text
Pelanggan
    |
    | Data Pesanan
    ↓
(1. Memproses Pesanan)
    |
    ↓
D1 Data Pesanan
```

---

# 6. DATA FLOW

## 6.1. Pengertian

Data Flow adalah aliran data yang berpindah dari satu komponen ke komponen lainnya.

Data Flow dapat menghubungkan:

```text
External Entity → Process
Process → External Entity
Process → Process
Process → Data Store
Data Store → Process
```

Data Flow biasanya digambarkan menggunakan:

```text
──────────────→
```

---

## 6.2. Penamaan Data Flow

Data Flow harus menggunakan **kata benda** atau nama data.

Contoh:

```text
Data Pengajuan
Data Pengguna
Data Pesanan
Data Pembayaran
Dokumen Pengajuan
Laporan Evaluasi
Hasil Validasi
Permintaan Pasokan
Persetujuan Pengajuan
```

Hindari nama berupa kata kerja:

```text
Memproses Pesanan
Membuat Pengajuan
Memvalidasi Data
```

Karena kata kerja merupakan nama proses.

---

## 6.3. Arah Data Flow

Pastikan arah panah menunjukkan arah perpindahan data.

Contoh:

```text
Pelanggan
    |
    | Data Pesanan
    ↓
(1. Memproses Pesanan)
```

Artinya:

Pelanggan mengirim Data Pesanan kepada proses Memproses Pesanan.

---

# 7. PROCESS

## 7.1. Pengertian

Process adalah aktivitas yang mengolah data menjadi data atau informasi baru.

Contoh:

```text
(1. Memproses Pesanan)

(2. Memvalidasi Pengajuan)

(3. Mengelola Data Mitra)

(4. Membuat Laporan)
```

Process harus menggunakan **kata kerja**.

---

## 7.2. Penamaan Process

Gunakan nama yang menjelaskan aktivitas.

Contoh:

```text
Memproses Pengajuan
Memvalidasi Data
Mengelola Data Mitra
Menghasilkan Laporan
Memproses Pembayaran
Mengelola Persediaan
```

Hindari:

```text
Data Pengajuan
Data Mitra
Laporan
Database
```

Karena itu bukan proses.

---

## 7.3. Input dan Output

Setiap proses harus memiliki input dan output yang logis.

Contoh:

```text
Data Pengajuan
      ↓
(1. Memvalidasi Pengajuan)
      ↓
Hasil Validasi
```

Prinsip:

```text
Input
  ↓
PROCESS
  ↓
Output
```

Jangan membuat proses yang tidak memiliki input atau output tanpa alasan yang jelas.

---

# 8. DATA STORE

## 8.1. Pengertian

Data Store adalah tempat penyimpanan data yang digunakan oleh sistem.

Contoh:

```text
D1 Data Pengguna
D2 Data Pengajuan
D3 Data Mitra
D4 Data Evaluasi
D5 Data Dokumen
```

Dalam implementasi, Data Store dapat direpresentasikan oleh:

* Database.
* Tabel.
* File.
* Dokumen.
* Penyimpanan data lainnya.

Namun, DFD tidak harus menggambarkan struktur tabel database secara detail.

---

## 8.2. Aturan Data Store

Data Store hanya boleh berinteraksi dengan Process.

Contoh:

```text
Process
    |
    ↓
D1 Data Pengajuan
```

atau:

```text
D1 Data Pengajuan
    |
    ↓
Process
```

Tidak boleh:

```text
Pimpinan
    |
    ↓
D1 Data Pengajuan
```

Karena External Entity tidak boleh langsung mengakses Data Store dalam DFD konseptual.

---

# 9. LEVEL DFD

DFD menggunakan konsep **dekomposisi proses**.

Artinya, proses yang masih umum dapat dipecah menjadi proses yang lebih detail.

Struktur yang umum:

```text
Context Diagram
      ↓
DFD Level 0
      ↓
DFD Level 1
      ↓
DFD Level 2
      ↓
DFD Level 3
```

Tidak semua sistem harus memiliki semua level.

Gunakan level tambahan hanya jika dibutuhkan.

---

# 10. CONTEXT DIAGRAM

## 10.1. Tujuan

Context Diagram digunakan untuk memberikan gambaran paling umum mengenai sistem.

Context Diagram menjawab:

> Siapa saja yang berinteraksi dengan sistem dan data apa yang masuk atau keluar dari sistem?

---

## 10.2. Aturan Context Diagram

Context Diagram harus memiliki:

* Satu proses utama yang mewakili seluruh sistem.
* External Entity.
* Data Flow.

Context Diagram tidak boleh memiliki:

* Data Store.
* Proses internal yang detail.
* Subprocess.

Contoh:

```text
                 Pimpinan
                    |
             Persetujuan
                    ↓
+--------------------------------+
|                                |
|  (Sistem Informasi Kerja Sama) |
|                                |
+--------------------------------+
       ↑                 ↓
       |                 |
Data Pengajuan      Laporan
       |                 |
     Jurusan          Pimpinan
```

---

# 11. ATURAN CONTEXT DIAGRAM

Gunakan:

```text
1 Proses Utama
+
External Entity
+
Data Flow
```

Jangan menggunakan:

```text
1 Proses
+
Data Store
```

Contoh yang salah:

```text
(Pengajuan)
     ↓
D1 Data Pengajuan
```

Data Store harus diperkenalkan pada level yang lebih detail.

---

# 12. DFD LEVEL 0

Dalam beberapa referensi, istilah "Level 0" dapat digunakan untuk menyebut Context Diagram, sementara referensi lain menyebut Level 0 sebagai diagram yang sudah berisi beberapa proses utama.

Karena itu, proyek harus menentukan konvensi penamaan yang digunakan.

Untuk standar dokumen ini, gunakan:

```text
Context Diagram
    ↓
DFD Level 0
    ↓
DFD Level 1
```

Dengan definisi:

### Context Diagram

```text
1 proses utama
```

### DFD Level 0

```text
Beberapa proses utama
+
External Entity
+
Data Flow
+
Data Store jika diperlukan
```

Hal terpenting bukan istilah levelnya, tetapi **konsistensi penggunaan istilah dalam seluruh dokumentasi proyek**.

---

# 13. DFD LEVEL 0

DFD Level 0 menggambarkan proses utama dalam sistem.

Contoh:

```text
              Pelanggan
                  |
                  | Data Pesanan
                  ↓
          (1. Memproses Pesanan)
                  |
                  ↓
          D1 Data Pesanan
                  |
                  ↓
          (2. Membuat Laporan)
                  |
                  ↓
               Manajer
```

Contoh sistem yang lebih kompleks:

```text
Pelanggan
    |
    ↓
(1. Mengelola Pesanan)
    |
    ↓
D1 Data Pesanan

(2. Mengelola Pembayaran)
    |
    ↓
D2 Data Pembayaran

(3. Mengelola Persediaan)
    |
    ↓
D3 Data Persediaan

(4. Membuat Laporan)
    |
    ↓
Manajer
```

---

# 14. DEKOMPOSISI PROSES

Dekomposisi proses adalah proses memecah proses yang kompleks menjadi proses yang lebih kecil dan lebih detail.

Contoh:

```text
DFD Level 0

(1. Mengelola Pengajuan)
```

Dekomposisi:

```text
DFD Level 1

(1.1 Mengisi Pengajuan)
       ↓
(1.2 Validasi Data)
       ↓
(1.3 Menyimpan Pengajuan)
       ↓
(1.4 Mengirim Notifikasi)
```

Dekomposisi lebih lanjut:

```text
DFD Level 2

(1.2 Validasi Data)
        ↓
(1.2.1 Validasi Kelengkapan)
        ↓
(1.2.2 Validasi Dokumen)
        ↓
(1.2.3 Menentukan Hasil Validasi)
```

---

# 15. BALANCING DFD

Balancing adalah aturan penting dalam pembuatan DFD bertingkat.

Prinsipnya:

> Data Flow yang masuk dan keluar dari suatu proses pada level yang lebih tinggi harus tetap konsisten ketika proses tersebut dipecah ke level yang lebih rendah.

Contoh:

### Context Diagram

```text
Jurusan
   |
   | Data Pengajuan
   ↓
(Sistem Informasi Kerja Sama)
   |
   | Hasil Validasi
   ↓
Jurusan
```

Ketika didekomposisi:

### DFD Level 0

```text
Jurusan
   |
   | Data Pengajuan
   ↓
(1. Mengelola Pengajuan)
   |
   | Hasil Validasi
   ↓
Jurusan
```

Maka Data Flow eksternal tetap:

```text
Data Pengajuan
Hasil Validasi
```

Tidak boleh tiba-tiba berubah menjadi:

```text
Context:
Data Pengajuan

Level 0:
Data Pengajuan
Dokumen Pengajuan
Data User
Data Mitra
```

jika Data User dan Data Mitra tidak berasal dari atau menuju External Entity pada Context Diagram.

---

# 16. ATURAN BALANCING

Ketika melakukan dekomposisi:

```text
Level Atas
      ↓
Level Bawah
```

Pastikan:

* External Entity tetap sama.
* Data Flow eksternal tetap sama.
* Arah Data Flow tetap konsisten.
* Makna data tetap sama.

Yang boleh bertambah:

* Process internal.
* Data Store.
* Data Flow internal.

Contoh:

```text
Context Diagram

External Entity
      ↓
Process Utama
      ↓
External Entity
```

Dekomposisi:

```text
External Entity
      ↓
Process 1
      ↓
Data Store
      ↓
Process 2
      ↓
Process 3
      ↓
External Entity
```

---

# 17. ATURAN PENAMAAN DFD

Gunakan aturan berikut.

## External Entity

Gunakan kata benda:

```text
Pelanggan
Admin
Pimpinan
Mitra
```

## Data Flow

Gunakan nama data:

```text
Data Pengajuan
Dokumen Kerja Sama
Laporan Evaluasi
Hasil Validasi
```

## Process

Gunakan kata kerja:

```text
Mengelola Pengajuan
Memvalidasi Data
Membuat Laporan
Mengelola Mitra
```

## Data Store

Gunakan nama kumpulan data:

```text
D1 Data Pengguna
D2 Data Pengajuan
D3 Data Evaluasi
```

---

# 18. ATURAN PENOMORAN PROCESS

Gunakan penomoran yang konsisten.

Contoh DFD Level 0:

```text
1. Mengelola Pengajuan
2. Mengelola Evaluasi
3. Mengelola Data Mitra
4. Membuat Laporan
```

Jika Process 1 didekomposisi:

```text
1.1 Mengisi Pengajuan
1.2 Memvalidasi Pengajuan
1.3 Menyimpan Pengajuan
1.4 Mengirim Notifikasi
```

Jika Process 1.2 didekomposisi:

```text
1.2.1 Memeriksa Kelengkapan
1.2.2 Memeriksa Dokumen
1.2.3 Menentukan Hasil Validasi
```

Penomoran membantu menjaga keterlacakan antar level.

---

# 19. ATURAN LAYOUT DFD

Diagram harus dibuat:

* Rapi.
* Seimbang.
* Tidak terlalu padat.
* Mudah dibaca.
* Memiliki arah aliran yang jelas.

Prioritaskan:

```text
Kiri → Kanan
```

atau:

```text
Atas → Bawah
```

Contoh:

```text
External Entity
       ↓
    Process
       ↓
  Data Store
       ↓
    Process
       ↓
External Entity
```

---

# 20. HINDARI GARIS BERSILANGAN

Contoh buruk:

```text
Entity A ───────────────┐
                        │
Process B ──────────────┼────→ Entity C
                        │
Entity D ───────────────┘
```

Jika diagram terlalu kompleks:

* Pindahkan posisi elemen.
* Gunakan connector.
* Pecah diagram.
* Buat level DFD yang lebih detail.

Jangan mengorbankan keterbacaan hanya agar semua proses berada dalam satu halaman.

---

# 21. HINDARI DUPLIKASI DATA FLOW

Jangan menggambar Data Flow yang sama berulang kali tanpa alasan.

Contoh:

```text
Data Pengajuan
Data Pengajuan
Data Pengajuan
```

Jika data yang sama digunakan oleh beberapa proses, pastikan arah dan konteksnya jelas.

---

# 22. HINDARI PROSES YANG TIDAK LOGIS

Setiap proses harus memiliki transformasi data yang jelas.

Contoh yang tidak baik:

```text
(Data Pengajuan)
```

Tidak jelas apa yang dilakukan.

Lebih baik:

```text
(Memvalidasi Pengajuan)
```

atau:

```text
(Menyimpan Pengajuan)
```

---

# 23. HINDARI BLACK HOLE

Black Hole adalah proses yang memiliki input tetapi tidak menghasilkan output yang jelas.

Contoh:

```text
Data Pengajuan
      ↓
(1. Memproses Data)
```

Tidak ada output.

Perbaiki:

```text
Data Pengajuan
      ↓
(1. Memvalidasi Pengajuan)
      ↓
Hasil Validasi
```

---

# 24. HINDARI MIRACLE

Miracle adalah proses yang menghasilkan output tanpa input yang jelas.

Contoh:

```text
(1. Membuat Laporan)
      ↓
Laporan Evaluasi
```

Jika laporan membutuhkan data evaluasi, maka harus ada input:

```text
D1 Data Evaluasi
      ↓
(1. Membuat Laporan)
      ↓
Laporan Evaluasi
```

---

# 25. HINDARI DIRECT ACCESS

External Entity tidak boleh langsung mengakses Data Store.

Salah:

```text
Pimpinan
    ↓
D1 Data Pengajuan
```

Benar:

```text
Pimpinan
    ↓
(1. Mengelola Pengajuan)
    ↓
D1 Data Pengajuan
```

---

# 26. DFD DAN DATABASE

DFD tidak sama dengan ERD atau database schema.

Gunakan:

### DFD

Untuk:

```text
Aliran Data
```

### ERD

Untuk:

```text
Struktur Data
Relasi Entitas
```

### Class Diagram

Untuk:

```text
Struktur Object-Oriented
Class
Attribute
Method
Relationship
```

Jangan menggambarkan semua tabel database ke dalam DFD jika tabel tersebut tidak memiliki fungsi penting dalam aliran data.

---

# 27. DFD DAN UML

DFD dan UML dapat digunakan bersama.

Contoh:

```text
Analisis Sistem
       ↓
DFD
       ↓
Identifikasi Proses
       ↓
Use Case
       ↓
Activity Diagram
       ↓
Sequence Diagram
       ↓
Class Diagram
       ↓
Implementasi
```

Namun, DFD dan UML tidak harus selalu dibuat bersamaan.

Pemilihan diagram harus disesuaikan dengan pendekatan pengembangan sistem.

---

# 28. CONTOH STUDI KASUS SISTEM INFORMASI KERJA SAMA

Misalnya terdapat Sistem Informasi Kerja Sama.

External Entity:

```text
Jurusan
Unit Kerja
Pimpinan
Admin
Mitra
```

---

## Context Diagram

```text
                    Pimpinan
                       |
              Persetujuan / Validasi
                       ↓
Jurusan ───────→ +------------------------+ ───────→ Pimpinan
Data Pengajuan   |                        |           Laporan
                 | Sistem Informasi       |
Unit Kerja ─────→| Kerja Sama             |──────→ Mitra
Data Kerja Sama  |                        |           Informasi
                 +------------------------+
                       ↑
                       |
                      Admin
```

---

# 29. DFD LEVEL 0 SISTEM INFORMASI KERJA SAMA

Contoh proses utama:

```text
1. Mengelola Pengajuan Kerja Sama
2. Mengelola Data Mitra
3. Melakukan Validasi Pengajuan
4. Mengelola Evaluasi
5. Mengelola Pelaksanaan
6. Membuat Laporan
```

Data Store:

```text
D1 Data Pengguna
D2 Data Pengajuan
D3 Data Mitra
D4 Data Evaluasi
D5 Data Pelaksanaan
D6 Data Dokumen
```

Gambaran:

```text
Jurusan
   |
   | Data Pengajuan
   ↓
(1. Mengelola Pengajuan)
   |
   ↓
D2 Data Pengajuan
   |
   ↓
(3. Validasi Pengajuan)
   |
   | Hasil Validasi
   ↓
Pimpinan

Pimpinan
   |
   | Data Evaluasi
   ↓
(4. Mengelola Evaluasi)
   |
   ↓
D4 Data Evaluasi

D2 Data Pengajuan
   |
   ↓
(6. Membuat Laporan)
   |
   ↓
Pimpinan
```

---

# 30. DFD LEVEL 1 — PENGAJUAN KERJA SAMA

Process:

```text
1.1 Mengisi Pengajuan
1.2 Memvalidasi Kelengkapan
1.3 Menyimpan Pengajuan
1.4 Mengirim Pengajuan
1.5 Mengirim Notifikasi
```

Alur:

```text
Jurusan
   |
   | Data Pengajuan
   ↓
(1.1 Mengisi Pengajuan)
   |
   ↓
(1.2 Memvalidasi Kelengkapan)
   |
   | Data Valid
   ↓
(1.3 Menyimpan Pengajuan)
   |
   ↓
D2 Data Pengajuan
   |
   ↓
(1.4 Mengirim Pengajuan)
   |
   ↓
Pimpinan
```

---

# 31. DFD LEVEL 1 — VALIDASI PENGAJUAN

Process:

```text
3.1 Membuka Data Pengajuan
3.2 Memeriksa Data
3.3 Memeriksa Dokumen
3.4 Menentukan Keputusan
3.5 Menyimpan Hasil Validasi
3.6 Mengirim Notifikasi
```

Alur:

```text
D2 Data Pengajuan
       |
       ↓
(3.1 Membuka Data Pengajuan)
       |
       ↓
(3.2 Memeriksa Data)
       |
       ↓
(3.3 Memeriksa Dokumen)
       |
       ↓
(3.4 Menentukan Keputusan)
       |
       ↓
(3.5 Menyimpan Hasil Validasi)
       |
       ↓
D4 Data Evaluasi
       |
       ↓
(3.6 Mengirim Notifikasi)
       |
       ↓
Jurusan
```

---

# 32. DFD LEVEL 2

Gunakan Level 2 hanya jika proses pada Level 1 masih terlalu kompleks.

Contoh:

```text
3.2 Memeriksa Data
```

Dekomposisi:

```text
3.2.1 Memeriksa Identitas Pengusul
3.2.2 Memeriksa Data Mitra
3.2.3 Memeriksa Jenis Kerja Sama
3.2.4 Memeriksa Periode Kerja Sama
3.2.5 Menentukan Status Pemeriksaan
```

Gunakan Level 2 apabila proses tersebut memang membutuhkan penjelasan lebih detail.

---

# 33. WORKFLOW MEMBUAT DFD

Gunakan langkah berikut.

## Langkah 1 — Pahami Sistem

Identifikasi:

```text
Nama Sistem
Tujuan Sistem
Batas Sistem
Pengguna Sistem
Proses Bisnis
Data yang Digunakan
Output Sistem
```

---

## Langkah 2 — Identifikasi External Entity

Tanyakan:

> Siapa atau sistem apa yang berinteraksi dengan sistem?

Contoh:

```text
Admin
Pimpinan
Jurusan
Unit Kerja
Mitra
```

---

## Langkah 3 — Identifikasi Input dan Output

Untuk setiap External Entity, tanyakan:

```text
Data apa yang masuk?
Data apa yang keluar?
```

Contoh:

```text
Jurusan
→ Data Pengajuan

Sistem
→ Hasil Validasi
```

---

## Langkah 4 — Buat Context Diagram

Buat:

```text
1 Proses Utama
+
External Entity
+
Data Flow
```

Jangan masukkan Data Store.

---

## Langkah 5 — Identifikasi Proses Utama

Pecah sistem menjadi proses utama.

Contoh:

```text
1. Pengajuan
2. Validasi
3. Evaluasi
4. Pelaksanaan
5. Pelaporan
```

---

## Langkah 6 — Buat DFD Level 0

Tambahkan:

```text
Process
Data Flow
External Entity
Data Store
```

---

## Langkah 7 — Lakukan Dekomposisi

Jika proses masih terlalu kompleks:

```text
Process 1
↓
1.1
1.2
1.3
1.4
```

---

## Langkah 8 — Lakukan Balancing

Bandingkan:

```text
Context Diagram
        ↕
DFD Level 0
        ↕
DFD Level 1
        ↕
DFD Level 2
```

Pastikan aliran data eksternal tetap konsisten.

---

## Langkah 9 — Rapikan Visual

Periksa:

* Posisi elemen.
* Jarak.
* Arah panah.
* Garis silang.
* Penamaan.
* Penomoran.
* Keterbacaan.

---

## Langkah 10 — Validasi dengan Stakeholder

Pastikan DFD sesuai dengan proses bisnis sebenarnya.

Validasi dengan:

* User.
* Pemilik proses.
* Analis sistem.
* Developer.

---

# 34. CHECKLIST DFD

## Context Diagram

* [ ] Hanya memiliki satu proses utama.
* [ ] Semua External Entity sudah teridentifikasi.
* [ ] Semua input data sudah digambarkan.
* [ ] Semua output data sudah digambarkan.
* [ ] Tidak memiliki Data Store.
* [ ] Tidak memiliki proses internal tambahan.

---

## DFD Level 0

* [ ] Semua proses utama sudah teridentifikasi.
* [ ] External Entity konsisten dengan Context Diagram.
* [ ] Data Flow eksternal konsisten.
* [ ] Data Store digunakan secara logis.
* [ ] Semua proses memiliki input dan output.
* [ ] Tidak ada proses yang menjadi Black Hole.
* [ ] Tidak ada proses yang menjadi Miracle.

---

## DFD Level 1+

* [ ] Proses merupakan hasil dekomposisi dari level sebelumnya.
* [ ] Penomoran konsisten.
* [ ] External Entity tetap konsisten.
* [ ] Data Flow eksternal tetap konsisten.
* [ ] Balancing terpenuhi.
* [ ] Data Store digunakan secara logis.
* [ ] Tidak ada aliran data yang tidak jelas.
* [ ] Tidak ada proses yang tidak memiliki fungsi.

---

# 35. CHECKLIST VISUAL

* [ ] Tidak ada garis yang saling bertabrakan.
* [ ] Arah aliran data jelas.
* [ ] Jarak antar elemen konsisten.
* [ ] Ukuran elemen konsisten.
* [ ] Nama mudah dibaca.
* [ ] Penomoran mudah dipahami.
* [ ] Tidak terlalu banyak elemen dalam satu diagram.
* [ ] Diagram dapat dipahami tanpa penjelasan lisan.
* [ ] Jika terlalu kompleks, diagram dipecah menjadi level yang lebih detail.

---

# 36. ATURAN EMAS DFD

Gunakan 15 aturan berikut:

1. **DFD berfokus pada aliran data.**
2. **External Entity menggunakan kata benda.**
3. **Process menggunakan kata kerja.**
4. **Data Flow menggunakan nama data.**
5. **Data Store menggunakan nama kumpulan data.**
6. **External Entity tidak langsung terhubung ke Data Store.**
7. **Setiap proses harus memiliki transformasi data yang jelas.**
8. **Hindari Black Hole.**
9. **Hindari Miracle.**
10. **Gunakan Context Diagram untuk gambaran sistem paling umum.**
11. **Gunakan dekomposisi untuk memperjelas proses kompleks.**
12. **Jaga balancing antar level DFD.**
13. **Jaga konsistensi nama dan arah Data Flow.**
14. **Jangan membuat diagram terlalu padat.**
15. **Validasi DFD berdasarkan proses bisnis nyata.**

---

# 37. PRINSIP AKHIR

DFD yang baik bukanlah DFD yang memiliki banyak simbol.

DFD yang baik adalah DFD yang mampu menjelaskan:

```text
Siapa
  ↓
Mengirim Data Apa
  ↓
Ke Proses Mana
  ↓
Diproses Menjadi Apa
  ↓
Disimpan Di Mana
  ↓
Dikirim Ke Siapa
```

Gunakan prinsip:

```text
External Entity
      ↓
Data Flow
      ↓
Process
      ↓
Data Store
      ↓
Process
      ↓
Data Flow
      ↓
External Entity
```

Untuk sistem yang kompleks:

```text
Context Diagram
      ↓
DFD Level 0
      ↓
DFD Level 1
      ↓
DFD Level 2
```

Setiap level harus memiliki hubungan yang jelas.

```text
Level Atas
    ↓
Dekomposisi
    ↓
Level Bawah
```

Dan selalu pastikan:

```text
Input Level Atas
    =
Input Level Bawah

Output Level Atas
    =
Output Level Bawah
```

inilah prinsip utama **balancing**.

Pada akhirnya, DFD harus menjadi alat untuk memahami sistem, bukan sekadar gambar dokumentasi. Jika seseorang yang belum memahami sistem dapat melihat DFD dan memahami **sumber data, proses pengolahan, penyimpanan data, dan tujuan informasi**, maka DFD tersebut telah menjalankan fungsi utamanya dengan baik.
