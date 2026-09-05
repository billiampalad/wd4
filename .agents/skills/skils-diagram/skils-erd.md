# SKILLS ERD

## Panduan Standar Membuat Entity Relationship Diagram yang Terstruktur, Rapi, Konsisten, dan Siap Diimplementasikan ke Database

---

# 1. TUJUAN DOKUMEN

Dokumen ini menjadi panduan standar untuk membuat **Entity Relationship Diagram (ERD)** yang digunakan dalam:

* Analisis kebutuhan data.
* Perancangan database.
* Pemodelan struktur data.
* Identifikasi entitas.
* Identifikasi atribut.
* Identifikasi hubungan antar entitas.
* Penentuan Primary Key.
* Penentuan Foreign Key.
* Penentuan Cardinality.
* Perancangan relasi antar tabel.
* Dokumentasi database.
* Validasi struktur database sebelum implementasi.

Tujuan utama ERD adalah memberikan gambaran yang jelas mengenai:

> **Data apa saja yang disimpan oleh sistem, bagaimana data tersebut diorganisasikan, dan bagaimana hubungan antar data tersebut terbentuk.**

ERD harus dapat menjadi jembatan antara:

```text
Kebutuhan Bisnis
      ↓
Model Data
      ↓
ERD
      ↓
Database Schema
      ↓
Implementasi Database
```

ERD yang baik bukan hanya terlihat rapi, tetapi juga harus merepresentasikan kebutuhan sistem secara akurat dan dapat diterjemahkan secara logis menjadi tabel database.

---

# 2. KONSEP DASAR ERD

ERD atau **Entity Relationship Diagram** adalah diagram yang digunakan untuk memodelkan:

* Entitas.
* Atribut.
* Relasi.
* Cardinality.
* Primary Key.
* Foreign Key.
* Constraint hubungan data.

Secara sederhana:

```text
ENTITY
   ↓
ATTRIBUTE
   ↓
RELATIONSHIP
   ↓
CARDINALITY
   ↓
DATABASE STRUCTURE
```

Contoh:

```text
Anggota
   |
   | melakukan
   |
Peminjaman
   |
   | memiliki
   |
Buku
```

ERD membantu menjawab:

```text
Data apa yang disimpan?
Siapa atau apa yang memiliki data?
Bagaimana data saling berhubungan?
Berapa banyak data yang dapat berhubungan?
Data mana yang menjadi identitas unik?
Bagaimana tabel saling terhubung?
```

---

# 3. FUNGSI ERD

ERD digunakan untuk:

1. Memvisualisasikan struktur database.
2. Mengidentifikasi entitas dalam sistem.
3. Mengidentifikasi atribut setiap entitas.
4. Menentukan Primary Key.
5. Menentukan Foreign Key.
6. Menggambarkan hubungan antar entitas.
7. Menentukan Cardinality.
8. Mengurangi duplikasi data.
9. Membantu menjaga integritas data.
10. Menjadi acuan pembuatan database.
11. Memudahkan komunikasi antara analis sistem, developer, dan database administrator.

ERD dapat membantu mengubah kebutuhan konseptual menjadi struktur tabel database yang lebih jelas. Primary Key digunakan untuk membedakan setiap record, sedangkan Foreign Key digunakan untuk menghubungkan tabel dan menjaga integritas relasi antar data.

---

# 4. KOMPONEN UTAMA ERD

Komponen utama ERD adalah:

```text
1. Entity
2. Attribute
3. Relationship
4. Cardinality
5. Primary Key
6. Foreign Key
```

Komponen tambahan dapat digunakan sesuai kebutuhan:

```text
7. Composite Attribute
8. Multivalued Attribute
9. Derived Attribute
10. Weak Entity
11. Associative Entity
12. Participation Constraint
```

---

# 5. ENTITY

## 5.1. Pengertian

Entity atau entitas adalah objek atau konsep yang datanya perlu disimpan dalam sistem.

Contoh:

```text
User
Mitra
Jurusan
Unit Kerja
Kerja Sama
Pengajuan
Evaluasi
Dokumen
```

Entitas dapat berupa:

* Orang.
* Organisasi.
* Tempat.
* Objek.
* Transaksi.
* Aktivitas.
* Konsep bisnis.

---

## 5.2. Cara Menentukan Entity

Gunakan pertanyaan:

> "Apakah objek ini memiliki data yang perlu disimpan secara mandiri?"

Jika jawabannya ya, kemungkinan objek tersebut dapat menjadi Entity.

Contoh kebutuhan:

> Jurusan dapat mengajukan kerja sama dengan mitra.

Dari kebutuhan tersebut dapat ditemukan:

```text
Jurusan
Mitra
Pengajuan Kerja Sama
```

Kemudian dianalisis apakah ketiganya perlu menjadi entitas terpisah.

---

## 5.3. Penamaan Entity

Gunakan kata benda.

Contoh:

```text
User
Mitra
Kerja Sama
Pengajuan
Evaluasi
Dokumen
```

Hindari:

```text
Mengelola Mitra
Memvalidasi Pengajuan
Melakukan Evaluasi
```

Karena itu merupakan aktivitas atau proses.

---

# 6. ATTRIBUTE

## 6.1. Pengertian

Attribute adalah karakteristik atau properti yang dimiliki oleh Entity.

Contoh:

```text
User
├── id
├── nik
├── name
└── password
```

Entity:

```text
User
```

Attribute:

```text
id
nik
name
password
```

---

## 6.2. Contoh Attribute

### User

```text
id
nik
name
password
role_id
```

### Mitra

```text
id
nama_mitra
alamat
email
telepon
```

### Kerja Sama

```text
id
nomor_mou
tanggal_mou
periode_mulai
periode_selesai
status_dokumen
status_berlaku
```

---

## 6.3. Aturan Attribute

Setiap atribut harus:

* Memiliki tujuan yang jelas.
* Berhubungan dengan Entity.
* Tidak menyimpan data yang tidak diperlukan.
* Tidak menduplikasi data yang seharusnya berada di Entity lain.
* Memiliki tipe data yang sesuai.
* Memiliki constraint jika diperlukan.

---

# 7. PRIMARY KEY

## 7.1. Pengertian

Primary Key atau PK adalah atribut yang digunakan untuk mengidentifikasi setiap record secara unik.

Contoh:

```text
User
-----------------
PK id
   nik
   name
   password
```

Nilai Primary Key harus:

* Unik.
* Tidak boleh NULL.
* Mewakili satu record secara jelas.

Primary Key merupakan bagian penting dalam menjaga identitas unik setiap record. Pemilihan key sebaiknya mempertimbangkan keunikan, stabilitas, dan kebutuhan sistem.

---

## 7.2. Contoh

```text
User

PK id
```

Data:

```text
id = 1
id = 2
id = 3
```

Tidak boleh:

```text
id = 1
id = 1
id = 2
```

---

## 7.3. Surrogate Key

Dalam aplikasi modern, sering digunakan ID buatan sistem.

Contoh:

```text
id BIGINT
```

atau:

```text
id UUID
```

Contoh:

```text
User
---------
PK id
   nik
   name
```

`id` menjadi identitas internal database, sedangkan `nik` dapat diberi constraint `UNIQUE` jika memang harus unik.

---

# 8. FOREIGN KEY

## 8.1. Pengertian

Foreign Key atau FK adalah atribut yang digunakan untuk menghubungkan sebuah tabel dengan tabel lainnya.

Contoh:

```text
User
---------
PK id

Profile
---------
PK id
FK user_id
```

Relasi:

```text
User.id
   ↑
   |
Profile.user_id
```

Foreign Key biasanya mengacu pada Primary Key tabel lain.

Foreign Key membantu menjaga integritas referensial karena hubungan antar data tidak hanya diasumsikan oleh aplikasi, tetapi dapat ditegakkan oleh struktur database.

---

# 9. RELATIONSHIP

## 9.1. Pengertian

Relationship adalah hubungan antara satu Entity dengan Entity lainnya.

Contoh:

```text
User
  |
  | memiliki
  ↓
Profile
```

Contoh lain:

```text
Jurusan
   |
   | mengajukan
   ↓
Pengajuan
```

Relationship harus menggambarkan hubungan bisnis yang benar.

---

## 9.2. Gunakan Kata Kerja

Nama relasi sebaiknya berupa kata kerja.

Contoh:

```text
User
  |
  | memiliki
  ↓
Profile
```

```text
Jurusan
  |
  | mengajukan
  ↓
Kerja Sama
```

```text
Pimpinan
  |
  | memvalidasi
  ↓
Pengajuan
```

---

# 10. CARDINALITY

Cardinality menunjukkan jumlah hubungan antar Entity.

Jenis umum:

```text
1 : 1
1 : N
N : 1
M : N
```

Dengan variasi opsionalitas:

```text
0..1
1
0..*
1..*
```

---

# 11. ONE TO ONE

## 11.1. Konsep

Satu record pada Entity A berhubungan dengan satu record pada Entity B.

Contoh:

```text
User 1 ─────── 1 Profile
```

Artinya:

```text
1 User
=
1 Profile
```

Contoh penggunaan:

```text
User
Profile
```

Namun, hubungan One-to-One harus digunakan jika aturan bisnis memang mensyaratkan hubungan tersebut.

---

# 12. ONE TO MANY

## 12.1. Konsep

Satu record pada Entity A dapat memiliki banyak record pada Entity B.

Contoh:

```text
Jurusan 1 ─────── 0..* Pengajuan
```

Artinya:

```text
1 Jurusan
↓
Banyak Pengajuan
```

Contoh lain:

```text
Pimpinan 1 ─────── 0..* Evaluasi
```

Satu pimpinan dapat melakukan banyak evaluasi.

---

## 12.2. Implementasi Database

Jika:

```text
Jurusan 1
    ↓
Pengajuan Banyak
```

Maka Foreign Key diletakkan pada sisi "many".

```text
Jurusan
---------
PK id

Pengajuan
---------
PK id
FK jurusan_id
```

Prinsip:

```text
ONE
 ↓
MANY
 ↓
FK berada di tabel MANY
```

---

# 13. MANY TO ONE

Many-to-One merupakan perspektif terbalik dari One-to-Many.

Contoh:

```text
Banyak Pengajuan
       ↓
Satu Jurusan
```

Database:

```text
Pengajuan
---------
PK id
FK jurusan_id
```

Satu `jurusan_id` dapat muncul pada banyak record Pengajuan.

---

# 14. MANY TO MANY

## 14.1. Konsep

Many-to-Many terjadi ketika:

```text
A memiliki banyak B
dan
B memiliki banyak A
```

Contoh:

```text
Mahasiswa
    ↕
Mata Kuliah
```

Satu mahasiswa dapat mengambil banyak mata kuliah.

Satu mata kuliah dapat diikuti banyak mahasiswa.

---

## 14.2. Solusi

Gunakan Associative Entity atau tabel penghubung.

Contoh:

```text
Mahasiswa
    |
    | 1
    |
    | N
Pendaftaran
    |
    | N
    |
    | 1
Mata Kuliah
```

Tabel:

```text
Mahasiswa
---------
PK id

MataKuliah
---------
PK id

Pendaftaran
---------
PK id
FK mahasiswa_id
FK mata_kuliah_id
```

Atau gunakan Composite Key:

```text
PK mahasiswa_id
PK mata_kuliah_id
```

Tabel penghubung atau bridge table merupakan pola penting untuk mengimplementasikan hubungan Many-to-Many dalam database relasional.

---

# 15. OPTIONALITY

Selain Cardinality, perhatikan apakah hubungan bersifat wajib atau opsional.

Gunakan:

```text
0..1
```

Artinya:

```text
Nol atau satu
```

Gunakan:

```text
0..*
```

Artinya:

```text
Nol atau banyak
```

Gunakan:

```text
1
```

Artinya:

```text
Tepat satu
```

Gunakan:

```text
1..*
```

Artinya:

```text
Minimal satu atau banyak
```

Contoh:

```text
User 1 ─────── 0..1 Profile
```

Artinya:

Satu User dapat memiliki nol atau satu Profile.

Sedangkan:

```text
User 1 ─────── 1 Profile
```

Artinya:

Setiap User wajib memiliki tepat satu Profile.

---

# 16. BEDAKAN CARDINALITY DAN OPTIONALITY

Jangan hanya menentukan:

```text
1 : N
```

Tanyakan juga:

```text
Apakah hubungan tersebut wajib?
Apakah boleh kosong?
Apakah minimal harus satu?
```

Contoh:

```text
User 1 ─────── 0..* Cooperation
```

Artinya:

Satu User dapat memiliki nol atau banyak Cooperation.

Sedangkan:

```text
User 1 ─────── 1..* Cooperation
```

Artinya:

Satu User harus memiliki minimal satu Cooperation.

---

# 17. ASSOCIATIVE ENTITY

Gunakan Associative Entity ketika hubungan Many-to-Many membutuhkan data tambahan.

Contoh:

```text
Mahasiswa
    |
    |
Pendaftaran
    |
    |
Mata Kuliah
```

Pendaftaran dapat memiliki:

```text
id
mahasiswa_id
mata_kuliah_id
tanggal_daftar
nilai
status
```

Dengan demikian, tabel penghubung bukan hanya menyimpan FK, tetapi juga informasi tentang hubungan tersebut.

---

# 18. ERD DAN TABEL DATABASE

Dalam model database relasional:

```text
Entity
    ↓
Table

Attribute
    ↓
Column

Record
    ↓
Row

Primary Key
    ↓
Unique Identifier

Relationship
    ↓
Foreign Key
```

Contoh:

### ERD

```text
User
   |
   | memiliki
   ↓
Profile
```

### Database

```text
users
---------
id
name

profiles
---------
id
user_id
jabatan
```

Relationship:

```text
users.id
    ↑
    |
profiles.user_id
```

---

# 19. ENTITY TIDAK SELALU LANGSUNG MENJADI TABEL

Pada tahap konseptual, Entity menggambarkan objek bisnis.

Pada tahap logical database design, Entity dapat dipetakan menjadi:

* Tabel.
* Tabel penghubung.
* Struktur turunan.
* Relasi khusus.

Contoh:

```text
Mahasiswa
    ↕
Mata Kuliah
```

Karena Many-to-Many, implementasinya menjadi:

```text
Mahasiswa
Mata_Kuliah
Pendaftaran
```

Jangan memaksakan setiap Entity konseptual menjadi satu tabel tanpa menganalisis Cardinality dan struktur relasinya.

---

# 20. NORMALISASI SEDERHANA

ERD harus membantu mengurangi:

* Redundansi data.
* Duplikasi data.
* Inkonsistensi data.

Contoh buruk:

```text
Pengajuan
----------------------------------
id
nama_mitra
alamat_mitra
telepon_mitra
nama_mitra
alamat_mitra
```

Data Mitra sebaiknya dipisahkan:

```text
Mitra
---------
PK id
nama
alamat
telepon

Pengajuan
---------
PK id
FK mitra_id
```

Sehingga:

```text
Pengajuan
    |
    | memiliki
    ↓
Mitra
```

Data mitra cukup disimpan satu kali.

---

# 21. HINDARI DATA DUPLIKAT

Jika data yang sama digunakan berkali-kali, pertimbangkan apakah data tersebut seharusnya dipisahkan menjadi Entity tersendiri.

Contoh:

Buruk:

```text
Kerja Sama
----------------
id
nama_mitra
alamat_mitra
telepon_mitra
nama_mitra_2
alamat_mitra_2
```

Lebih baik:

```text
Mitra
---------
PK id
nama
alamat
telepon

Kerja Sama
---------
PK id

Kerja Sama Mitra
---------
FK kerja_sama_id
FK mitra_id
```

Jika satu kerja sama memang dapat melibatkan banyak mitra, gunakan tabel penghubung.

---

# 22. MULTI-VALUED ATTRIBUTE

Hindari menyimpan banyak nilai dalam satu kolom.

Buruk:

```text
nomor_telepon
----------------
081xxx, 082xxx, 083xxx
```

Jika satu User memang dapat memiliki banyak nomor telepon, pertimbangkan:

```text
User
---------
PK id

User_Telephone
---------
PK id
FK user_id
nomor_telepon
```

Ini membuat data lebih terstruktur dan mudah dikelola.

Multi-valued attribute sering lebih baik dipisahkan menjadi tabel tersendiri jika jumlah nilainya dapat bertambah.

---

# 23. COMPOSITE ATTRIBUTE

Composite Attribute adalah atribut yang dapat dipecah menjadi beberapa atribut yang lebih kecil.

Contoh:

```text
Nama Lengkap
```

Dapat menjadi:

```text
nama_depan
nama_tengah
nama_belakang
```

Namun, tidak selalu harus dipecah.

Keputusan harus berdasarkan kebutuhan sistem.

Jika sistem hanya membutuhkan:

```text
Nama Lengkap
```

maka:

```text
nama
```

sudah cukup.

---

# 24. DERIVED ATTRIBUTE

Derived Attribute adalah atribut yang dapat dihitung dari data lain.

Contoh:

```text
Tanggal Lahir
     ↓
Usia
```

Jika usia dapat dihitung dari tanggal lahir, biasanya tidak perlu disimpan sebagai kolom permanen.

Gunakan:

```text
tanggal_lahir
```

Kemudian hitung:

```text
usia = tanggal_sekarang - tanggal_lahir
```

Hal ini membantu menghindari data yang menjadi tidak akurat karena tidak diperbarui.

---

# 25. WEAK ENTITY

Weak Entity adalah Entity yang keberadaannya bergantung pada Entity lain.

Contoh konsep:

```text
Order
   |
   ↓
Order Detail
```

Order Detail tidak memiliki makna jika Order induknya tidak ada.

Dalam model tertentu, identitas Weak Entity dapat bergantung pada Primary Key Entity induknya dan sebuah discriminator.

Dalam implementasi database modern, konsep ini dapat diterapkan menggunakan:

```text
FK order_id
```

dan kombinasi key tertentu jika dibutuhkan.

---

# 26. RELASI SELF-REFERENCE

Entity dapat berhubungan dengan dirinya sendiri.

Contoh:

```text
Employee
    |
    | supervises
    ↓
Employee
```

Implementasi:

```text
Employee
---------
PK id
FK manager_id
name
```

`manager_id` mengacu kembali ke `Employee.id`.

Gunakan ini untuk struktur seperti:

* Atasan dan bawahan.
* Kategori dan subkategori.
* Parent dan child.
* Struktur organisasi.

---

# 27. CONSTRAINT DAN INTEGRITAS DATA

ERD yang baik harus memperhatikan aturan bisnis.

Contoh:

```text
Satu User harus memiliki satu NIK unik.
```

Maka:

```text
nik UNIQUE
```

Contoh:

```text
Satu Pengajuan harus dimiliki oleh User.
```

Maka:

```text
created_by NOT NULL
```

Contoh:

```text
Satu Evaluation harus terkait dengan Cooperation.
```

Maka:

```text
cooperation_id FK NOT NULL
```

Cardinality dan participation constraint membantu menerjemahkan aturan hubungan tersebut ke dalam model database.

---

# 28. ON DELETE DAN ON UPDATE

Ketika ERD akan diterjemahkan ke database, pertimbangkan perilaku Foreign Key.

Contoh:

```text
ON DELETE CASCADE
ON DELETE SET NULL
ON DELETE RESTRICT
```

Gunakan dengan hati-hati.

### CASCADE

Jika parent dihapus, child ikut dihapus.

Cocok untuk:

```text
Order
    ↓
Order Detail
```

Namun berbahaya jika digunakan pada data penting.

### SET NULL

Jika parent dihapus, FK child menjadi NULL.

Gunakan jika hubungan bersifat opsional.

### RESTRICT

Mencegah parent dihapus jika masih memiliki child.

Cocok untuk data historis atau data referensi penting.

---

# 29. CONTOH ERD SISTEM INFORMASI KERJA SAMA

Misalnya sistem memiliki:

```text
User
Role
Profile
Mitra
Kerja Sama
Pengajuan
Evaluasi
Dokumentasi
Notifikasi
```

Struktur konseptual:

```text
Role
  |
  | memiliki
  ↓
User
  |
  | memiliki
  ↓
Profile


User
  |
  | membuat
  ↓
Pengajuan
  |
  | terkait
  ↓
Mitra


Pengajuan
  |
  | dievaluasi
  ↓
Evaluasi


Pengajuan
  |
  | memiliki
  ↓
Dokumentasi


User
  |
  | menerima
  ↓
Notifikasi
```

---

# 30. CONTOH STRUKTUR TABEL

### roles

```text
roles
----------------
PK id
role_name
```

### users

```text
users
----------------
PK id
nik
name
password
FK role_id
```

### profiles

```text
profiles
----------------
PK id
FK user_id
jabatan
nama_jurusan
nama_unit
```

### mitras

```text
mitras
----------------
PK id
nama_mitra
alamat
email
telepon
```

### cooperations

```text
cooperations
----------------
PK id
FK mitra_id
FK created_by
status_dokumen
status_berlaku
```

### evaluasis

```text
evaluasis
----------------
PK id
FK cooperation_id
FK evaluator_id
score
conclusion
```

---

# 31. CONTOH CARDINALITY

```text
Role 1 ───────── 0..* User
```

Artinya:

Satu Role dapat digunakan oleh banyak User.

```text
User 1 ───────── 0..1 Profile
```

Artinya:

Satu User memiliki nol atau satu Profile.

```text
Mitra 1 ───────── 0..* Cooperation
```

Artinya:

Satu Mitra dapat memiliki banyak Cooperation.

```text
Cooperation 1 ───────── 0..* Evaluation
```

Artinya:

Satu Cooperation dapat memiliki banyak Evaluation.

```text
User 1 ───────── 0..* Evaluation
```

Artinya:

Satu User dapat melakukan banyak Evaluation.

---

# 32. WORKFLOW PEMBUATAN ERD

Gunakan workflow berikut.

```text
Kebutuhan Sistem
      ↓
Identifikasi Data
      ↓
Identifikasi Entity
      ↓
Identifikasi Attribute
      ↓
Tentukan Primary Key
      ↓
Identifikasi Relationship
      ↓
Tentukan Cardinality
      ↓
Tentukan Foreign Key
      ↓
Normalisasi
      ↓
Validasi Aturan Bisnis
      ↓
Buat ERD
      ↓
Mapping ke Database
      ↓
Buat Migration / Schema
```

---

# 33. LANGKAH 1 — IDENTIFIKASI ENTITY

Ambil dari kebutuhan sistem.

Contoh:

> Jurusan mengajukan kerja sama kepada Pimpinan untuk divalidasi.

Identifikasi:

```text
Jurusan
Kerja Sama
Pengajuan
Pimpinan
```

Kemudian analisis apakah Pimpinan perlu menjadi Entity tersendiri atau cukup direpresentasikan sebagai Role/User.

---

# 34. LANGKAH 2 — IDENTIFIKASI ATTRIBUTE

Contoh:

```text
Mitra
```

Atribut:

```text
id
nama_mitra
alamat
email
telepon
```

Pastikan setiap atribut memang dimiliki oleh Entity tersebut.

---

# 35. LANGKAH 3 — TENTUKAN PRIMARY KEY

Setiap Entity yang menjadi tabel harus memiliki identitas unik.

Contoh:

```text
Mitra
---------
PK id
```

```text
Pengajuan
---------
PK id
```

```text
Evaluasi
---------
PK id
```

---

# 36. LANGKAH 4 — IDENTIFIKASI RELATIONSHIP

Tanyakan:

```text
Siapa memiliki siapa?
Siapa membuat siapa?
Siapa melakukan apa?
Data mana yang bergantung pada data lain?
```

Contoh:

```text
User
  |
  | membuat
  ↓
Pengajuan
```

---

# 37. LANGKAH 5 — TENTUKAN CARDINALITY

Jangan menebak Cardinality.

Gunakan aturan bisnis.

Tanyakan:

```text
Satu A dapat memiliki berapa B?
Satu B dapat memiliki berapa A?
Apakah hubungan wajib?
Apakah boleh kosong?
```

Contoh:

> Satu Jurusan dapat mengajukan banyak Pengajuan.

Maka:

```text
Jurusan 1 ───── 0..* Pengajuan
```

---

# 38. LANGKAH 6 — TENTUKAN FOREIGN KEY

Setelah Cardinality diketahui, tentukan FK.

Contoh:

```text
Jurusan 1
    ↓
Pengajuan Banyak
```

Maka:

```text
Pengajuan
---------
PK id
FK jurusan_id
```

---

# 39. LANGKAH 7 — ATASI MANY-TO-MANY

Jika ditemukan:

```text
A M ───── N B
```

Jangan langsung membuat FK di kedua tabel.

Buat Entity penghubung:

```text
A
 |
 |
A_B
 |
 |
B
```

Contoh:

```text
Cooperation
      ↕
Cooperation_Mitra
      ↕
Mitra
```

---

# 40. LANGKAH 8 — NORMALISASI

Periksa:

```text
Apakah ada data yang berulang?
Apakah satu kolom menyimpan banyak nilai?
Apakah data dapat dipisahkan?
Apakah ada atribut yang tidak bergantung pada Entity?
```

Tujuannya:

```text
Mengurangi Redundansi
+
Meningkatkan Konsistensi
+
Memudahkan Pemeliharaan
```

---

# 41. LANGKAH 9 — VALIDASI ERD

Bandingkan ERD dengan kebutuhan sistem.

Pastikan:

```text
Requirement
    ↓
Entity
    ↓
Attribute
    ↓
Relationship
    ↓
Cardinality
    ↓
Database
```

Semuanya harus konsisten.

---

# 42. ERD DAN DFD

DFD dan ERD memiliki fokus berbeda.

### DFD

Menjelaskan:

```text
Bagaimana data bergerak?
```

### ERD

Menjelaskan:

```text
Bagaimana data disimpan dan berhubungan?
```

Contoh:

```text
DFD
Jurusan
   ↓
Data Pengajuan
   ↓
Proses Pengajuan
   ↓
Database
```

ERD:

```text
Jurusan
   |
   | 1:N
   ↓
Pengajuan
```

DFD menjelaskan **aliran data**.

ERD menjelaskan **struktur data**.

Keduanya dapat digunakan bersama.

---

# 43. ERD DAN DFD DALAM ANALISIS SISTEM

Gunakan:

```text
Kebutuhan Sistem
       ↓
DFD
       ↓
Identifikasi Aliran Data
       ↓
ERD
       ↓
Identifikasi Struktur Data
       ↓
Database Schema
```

Dengan demikian:

```text
DFD = Data Bergerak
ERD = Data Disimpan
```

---

# 44. ERD DAN UML CLASS DIAGRAM

ERD dan Class Diagram memiliki beberapa kesamaan visual, tetapi tujuan utamanya berbeda.

### ERD

Fokus pada:

```text
Database
Entity
Attribute
Relationship
PK
FK
Cardinality
```

### Class Diagram

Fokus pada:

```text
Object
Class
Attribute
Method
Inheritance
Association
Dependency
```

Contoh:

ERD:

```text
User
---------
PK id
name
```

Class Diagram:

```text
User
---------
- id
- name
---------
+ login()
+ logout()
```

ERD tidak perlu memasukkan method seperti:

```text
login()
logout()
```

Karena method merupakan konsep perilaku objek, bukan struktur penyimpanan data.

---

# 45. STANDAR VISUAL ERD

Gunakan prinsip:

## 1. Konsisten

Gunakan format yang sama untuk semua Entity.

Contoh:

```text
+-----------------------+
| TABLE NAME            |
+-----------------------+
| PK id                 |
| FK user_id            |
| name                  |
+-----------------------+
```

---

## 2. Rapi

Atur posisi tabel agar:

* Tidak bertumpuk.
* Tidak saling menutupi.
* Tidak terlalu jauh.
* Garis relasi mudah diikuti.

---

## 3. Minimalkan Garis Silang

Contoh buruk:

```text
A ────────────────┐
                  │
B ────────┐       │
          │       │
C ────────┴───────┘
```

Atur ulang posisi Entity.

---

## 4. Kelompokkan Berdasarkan Domain

Contoh:

```text
AUTHENTICATION
User
Role
Profile

COOPERATION
Cooperation
Mitra
JenisKerjaSama

EVALUATION
Evaluation
Conclusion
Suggestion

DOCUMENTATION
Documentation
```

Ini sangat membantu untuk database yang besar.

---

# 46. STANDAR PENAMAAN DATABASE

Gunakan konvensi yang konsisten.

Contoh:

```text
users
roles
profiles
mitras
cooperations
evaluasis
notifikasis
```

Untuk Foreign Key:

```text
user_id
role_id
mitra_id
cooperation_id
```

Gunakan pola:

```text
<nama_entity>_id
```

Contoh:

```text
FK user_id
FK mitra_id
FK cooperation_id
```

Hindari variasi:

```text
userID
id_user
userid
```

dalam satu proyek yang sama jika tidak ada alasan khusus.

---

# 47. CHECKLIST ENTITY

* [ ] Semua objek bisnis utama sudah ditemukan.
* [ ] Tidak ada Entity yang sebenarnya hanya atribut.
* [ ] Tidak ada Entity yang sebenarnya hanya proses.
* [ ] Nama Entity konsisten.
* [ ] Setiap Entity memiliki tujuan yang jelas.
* [ ] Setiap Entity memiliki Primary Key.

---

# 48. CHECKLIST ATTRIBUTE

* [ ] Setiap Entity memiliki atribut yang relevan.
* [ ] Tidak ada atribut duplikat.
* [ ] Tidak ada atribut yang menyimpan banyak nilai tanpa alasan.
* [ ] Tipe data dapat ditentukan.
* [ ] Primary Key sudah ditentukan.
* [ ] Foreign Key sudah ditentukan.
* [ ] Constraint penting sudah diidentifikasi.

---

# 49. CHECKLIST RELATIONSHIP

* [ ] Semua hubungan antar Entity sudah diidentifikasi.
* [ ] Nama hubungan masuk akal.
* [ ] Cardinality sudah ditentukan.
* [ ] Optionality sudah dipertimbangkan.
* [ ] Foreign Key sesuai dengan hubungan.
* [ ] Many-to-Many sudah memiliki tabel penghubung.
* [ ] Tidak ada hubungan yang tidak memiliki alasan bisnis.

---

# 50. CHECKLIST DATABASE

* [ ] Setiap tabel memiliki Primary Key.
* [ ] Foreign Key mengacu pada tabel yang benar.
* [ ] Tidak ada FK yang tidak memiliki tujuan.
* [ ] Tidak ada data duplikat yang tidak diperlukan.
* [ ] Tidak ada multi-valued attribute yang seharusnya dipisahkan.
* [ ] Constraint sudah dipertimbangkan.
* [ ] ON DELETE sudah ditentukan jika diperlukan.
* [ ] ON UPDATE sudah ditentukan jika diperlukan.
* [ ] Struktur ERD sesuai dengan kebutuhan aplikasi.

---

# 51. CHECKLIST VISUAL

* [ ] Semua tabel memiliki format yang konsisten.
* [ ] Nama tabel mudah dibaca.
* [ ] PK dan FK terlihat jelas.
* [ ] Garis relasi tidak saling bertabrakan.
* [ ] Cardinality mudah dibaca.
* [ ] Tidak ada tabel yang bertumpuk.
* [ ] Tabel dikelompokkan berdasarkan domain jika diperlukan.
* [ ] Diagram tetap dapat dipahami tanpa penjelasan tambahan.

---

# 52. VALIDASI AKHIR ERD

Sebelum ERD digunakan sebagai dasar implementasi database, lakukan validasi berikut:

```text
Kebutuhan Sistem
       ↓
Apakah semua data yang diperlukan tersedia?
       ↓
Entity
       ↓
Apakah semua Entity benar?
       ↓
Attribute
       ↓
Apakah semua atribut berada di tempat yang tepat?
       ↓
Relationship
       ↓
Apakah hubungan antar Entity benar?
       ↓
Cardinality
       ↓
Apakah jumlah hubungan sesuai aturan bisnis?
       ↓
PK / FK
       ↓
Apakah integritas referensial dapat dijaga?
       ↓
Normalisasi
       ↓
Apakah redundansi sudah diminimalkan?
       ↓
Database Schema
       ↓
Siap diimplementasikan
```

---

# 53. ATURAN EMAS ERD

Gunakan 15 aturan berikut:

1. **Mulai dari kebutuhan bisnis, bukan langsung dari tabel database.**
2. **Identifikasi Entity sebelum menentukan Attribute.**
3. **Setiap Entity harus memiliki identitas yang jelas.**
4. **Tentukan Primary Key untuk setiap tabel.**
5. **Gunakan Foreign Key untuk merepresentasikan hubungan antar tabel.**
6. **Tentukan Cardinality berdasarkan aturan bisnis, bukan tebakan.**
7. **Bedakan Cardinality dengan Optionality.**
8. **Letakkan Foreign Key pada sisi yang tepat berdasarkan Cardinality.**
9. **Gunakan tabel penghubung untuk Many-to-Many.**
10. **Hindari penyimpanan data yang sama berulang kali.**
11. **Hindari menyimpan banyak nilai dalam satu kolom jika seharusnya menjadi data terpisah.**
12. **Gunakan constraint untuk menjaga integritas data.**
13. **Pastikan ERD dapat dipetakan secara logis ke database.**
14. **Jaga konsistensi penamaan Entity, Attribute, PK, dan FK.**
15. **Validasi ERD dengan kebutuhan bisnis sebelum membuat migration atau database.**

---

# 54. PRINSIP AKHIR

ERD yang baik bukanlah ERD yang memiliki tabel paling banyak.

ERD yang baik adalah ERD yang:

```text
Merepresentasikan Kebutuhan Bisnis
            +
Memiliki Struktur Data yang Jelas
            +
Memiliki Relasi yang Benar
            +
Memiliki Cardinality yang Tepat
            +
Memiliki PK dan FK yang Konsisten
            +
Minim Redundansi
            +
Menjaga Integritas Data
            +
Mudah Dipahami
            +
Siap Diimplementasikan
```

Gunakan prinsip:

```text
Business Requirement
        ↓
Entity
        ↓
Attribute
        ↓
Primary Key
        ↓
Relationship
        ↓
Cardinality
        ↓
Foreign Key
        ↓
Normalization
        ↓
Constraint
        ↓
Database Schema
```

Selalu pastikan:

```text
ERD
  ↓
Sesuai dengan Kebutuhan Bisnis

ERD
  ↓
Sesuai dengan DFD

ERD
  ↓
Sesuai dengan UML jika digunakan

ERD
  ↓
Sesuai dengan Database

Database
  ↓
Sesuai dengan Implementasi Aplikasi
```

Dengan pendekatan tersebut, ERD tidak hanya menjadi diagram visual, tetapi menjadi **blueprint struktur data** yang dapat digunakan sebagai dasar untuk membuat database, migration, model Laravel, relasi Eloquent, dan implementasi aplikasi.

Prinsip paling penting:

> **Jangan membuat ERD berdasarkan tabel yang ingin dibuat. Buat ERD berdasarkan data dan hubungan bisnis yang benar, kemudian turunkan ERD tersebut menjadi struktur tabel database.**