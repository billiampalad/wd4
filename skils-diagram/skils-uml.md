# SKILLS UML

## Panduan Standar Membuat Diagram UML yang Terstruktur, Rapi, Konsisten, dan Mudah Dipahami

---

## 1. Tujuan Dokumen

Dokumen ini menjadi panduan dan standar dalam membuat diagram UML (Unified Modeling Language) untuk kebutuhan analisis, perancangan, dokumentasi, dan pengembangan sistem perangkat lunak.

Tujuan utama penerapan standar ini adalah memastikan bahwa setiap diagram UML yang dibuat:

* Memiliki struktur yang jelas.
* Mudah dibaca oleh developer, analis sistem, user, dan stakeholder.
* Menggambarkan kebutuhan sistem secara akurat.
* Tidak memiliki elemen yang ambigu atau tidak memiliki fungsi.
* Memiliki hubungan yang konsisten antar diagram.
* Dapat digunakan sebagai blueprint sebelum proses implementasi atau coding.
* Dapat dikembangkan dan diperbarui apabila kebutuhan sistem berubah.

UML merupakan bahasa pemodelan standar untuk memvisualisasikan, mendefinisikan, dan mendokumentasikan sistem. UML bukan bahasa pemrograman dan bukan metodologi pengembangan perangkat lunak, sehingga penggunaannya dapat disesuaikan dengan metode pengembangan sistem yang digunakan.

---

# 2. Prinsip Utama dalam Membuat UML

Setiap diagram UML harus mengikuti prinsip berikut.

## 2.1. Berdasarkan Kebutuhan Sistem

Diagram harus dibuat berdasarkan kebutuhan nyata dari sistem.

Sebelum membuat diagram, identifikasi:

1. Siapa yang menggunakan sistem?
2. Apa tujuan masing-masing pengguna?
3. Fitur apa yang tersedia?
4. Bagaimana alur prosesnya?
5. Data apa yang digunakan?
6. Siapa yang bertanggung jawab terhadap setiap proses?
7. Bagaimana hubungan antar data atau objek?
8. Apa kondisi yang mungkin terjadi dalam proses?

Jangan membuat diagram hanya berdasarkan struktur database atau nama menu aplikasi.

Diagram harus menggambarkan **perilaku dan struktur sistem yang sebenarnya**.

---

## 2.2. Gunakan UML untuk Level yang Tepat

Setiap diagram memiliki tujuan yang berbeda.

Gunakan:

* **Use Case Diagram** untuk menjelaskan siapa yang menggunakan sistem dan fitur apa yang dapat digunakan.
* **Activity Diagram** untuk menjelaskan bagaimana suatu proses berjalan dari awal sampai selesai.
* **Class Diagram** untuk menjelaskan struktur objek, atribut, method, dan hubungan antar class.
* **Sequence Diagram** untuk menjelaskan urutan komunikasi antara actor, user interface, controller, service, database, atau objek lainnya.
* **State Diagram** untuk menjelaskan perubahan status suatu objek.
* **Component Diagram** untuk menjelaskan struktur komponen perangkat lunak.
* **Deployment Diagram** untuk menjelaskan bagaimana sistem ditempatkan pada infrastruktur atau server.

Jangan memaksakan satu diagram untuk menjelaskan semua aspek sistem.

---

# 3. Alur Pembuatan UML yang Direkomendasikan

Gunakan urutan berikut ketika merancang sistem baru:

```text
Kebutuhan Sistem
      ↓
Identifikasi Aktor
      ↓
Identifikasi Fitur
      ↓
Use Case Diagram
      ↓
Deskripsi Use Case
      ↓
Activity Diagram
      ↓
Sequence Diagram
      ↓
Identifikasi Class / Object
      ↓
Class Diagram
      ↓
State Diagram
      ↓
Component / Deployment Diagram
      ↓
Validasi Konsistensi
      ↓
Implementasi Sistem
```

Urutan ini dapat disesuaikan dengan kebutuhan proyek.

Untuk sistem sederhana, minimal gunakan:

```text
Use Case Diagram
       ↓
Activity Diagram
       ↓
Class Diagram
```

Untuk sistem yang lebih kompleks, tambahkan:

```text
Sequence Diagram
State Diagram
Component Diagram
Deployment Diagram
```

---

# 4. Standar Umum Visual Diagram

Semua diagram harus mengikuti aturan visual berikut.

## 4.1. Gunakan Layout yang Konsisten

Diagram harus memiliki arah alur yang jelas.

Prioritaskan:

```text
Atas → Bawah
```

atau:

```text
Kiri → Kanan
```

Jangan mencampur arah secara berlebihan.

Contoh yang baik:

```text
Start
  ↓
Input Data
  ↓
Validasi
  ↓
[Valid?]
 /     \
Ya      Tidak
↓         ↓
Simpan   Kembali
  ↓
Selesai
```

---

## 4.2. Hindari Garis yang Saling Berpotongan

Usahakan hubungan antar elemen tidak menghasilkan banyak garis silang.

Jika diagram terlalu penuh:

* Atur ulang posisi elemen.
* Kelompokkan elemen berdasarkan fungsi.
* Pecah diagram menjadi beberapa diagram.
* Gunakan sub-diagram.
* Gunakan diagram khusus untuk proses tertentu.

Prinsip:

> Diagram yang lebih sederhana dan mudah dibaca lebih baik daripada diagram yang sangat lengkap tetapi sulit dipahami.

---

## 4.3. Gunakan Penamaan yang Konsisten

Gunakan istilah yang sama di seluruh diagram.

Contoh:

Jika Use Case menggunakan:

```text
Validasi Pengajuan
```

Maka Activity Diagram dan Sequence Diagram sebaiknya tetap menggunakan istilah:

```text
Validasi Pengajuan
```

Jangan berubah menjadi:

```text
Approval Pengajuan
```

atau:

```text
Pemeriksaan Data
```

kecuali memang memiliki makna proses yang berbeda.

---

## 4.4. Gunakan Nama yang Deskriptif

Gunakan nama yang langsung menjelaskan fungsi.

Contoh Use Case:

```text
Kelola Data Mitra
Ajukan Kerja Sama
Validasi Pengajuan
Evaluasi Kerja Sama
Cetak Laporan
```

Hindari:

```text
Proses 1
Menu A
Data
Fitur
Kelola
```

---

## 4.5. Satu Diagram Harus Memiliki Fokus

Setiap diagram harus menjawab satu pertanyaan utama.

Contoh:

### Use Case Diagram

> Siapa yang dapat melakukan apa?

### Activity Diagram

> Bagaimana proses berjalan?

### Sequence Diagram

> Bagaimana objek saling berkomunikasi?

### Class Diagram

> Apa struktur objek dan hubungan antar objek?

---

# 5. USE CASE DIAGRAM

## 5.1. Tujuan

Use Case Diagram digunakan untuk menggambarkan:

* Aktor yang menggunakan sistem.
* Fitur yang dapat digunakan.
* Hubungan antara aktor dan fitur.
* Batas sistem.

Use Case Diagram memberikan gambaran tingkat tinggi mengenai kebutuhan fungsional sistem dan membantu menentukan batas tanggung jawab sistem.

---

## 5.2. Elemen Utama

### Actor

Actor adalah pihak eksternal yang berinteraksi dengan sistem.

Contoh:

```text
Admin
Pimpinan
Jurusan
Unit Kerja
Mitra
Sistem Eksternal
```

Actor tidak selalu berupa manusia.

Actor dapat berupa:

* User.
* Organisasi.
* Sistem lain.
* Perangkat eksternal.

---

### Use Case

Use Case adalah fungsi yang disediakan sistem untuk actor.

Gunakan nama berupa kata kerja.

Contoh:

```text
Login
Mengelola Data Mitra
Mengajukan Kerja Sama
Memvalidasi Pengajuan
Memberikan Evaluasi
Menghasilkan Laporan
```

---

### System Boundary

System Boundary menunjukkan batas sistem.

Contoh:

```text
+--------------------------------------+
| Sistem Informasi Kerja Sama         |
|                                      |
|   (Login)                            |
|   (Kelola Data Mitra)               |
|   (Ajukan Kerja Sama)               |
|   (Validasi Pengajuan)              |
|                                      |
+--------------------------------------+
```

Actor berada di luar boundary.

Use Case berada di dalam boundary.

---

## 5.3. Association

Association digunakan untuk menunjukkan interaksi actor dengan Use Case.

Contoh:

```text
Pimpinan -------- (Validasi Pengajuan)
```

Satu actor dapat memiliki banyak Use Case.

---

## 5.4. Include

Gunakan `<<include>>` apabila suatu Use Case selalu membutuhkan Use Case lainnya.

Contoh:

```text
(Ajukan Kerja Sama)
        |
        | <<include>>
        ↓
(Validasi Data Pengajuan)
```

Artinya:

Setiap proses Ajukan Kerja Sama selalu menjalankan Validasi Data Pengajuan.

Gunakan Include untuk perilaku yang wajib atau selalu dijalankan.

---

## 5.5. Extend

Gunakan `<<extend>>` apabila suatu perilaku merupakan tambahan yang hanya terjadi pada kondisi tertentu.

Contoh:

```text
(Ajukan Perpanjangan)
        |
        | <<extend>>
        ↓
(Ajukan Kerja Sama)
```

Gunakan Extend apabila proses tambahan bersifat opsional atau bergantung pada kondisi tertentu.

---

## 5.6. Aturan Use Case Diagram

Gunakan aturan:

* Actor berada di luar System Boundary.
* Use Case berada di dalam System Boundary.
* Nama Use Case menggunakan kata kerja.
* Jangan memasukkan detail teknis database.
* Jangan memasukkan tabel database.
* Jangan menggambarkan alur proses secara detail.
* Jangan membuat satu Use Case terlalu besar.
* Gunakan Include hanya untuk perilaku wajib.
* Gunakan Extend untuk perilaku tambahan atau kondisional.
* Pastikan setiap Use Case memiliki tujuan bisnis yang jelas.

---

# 6. ACTIVITY DIAGRAM

## 6.1. Tujuan

Activity Diagram digunakan untuk menggambarkan alur aktivitas atau proses dalam sistem.

Activity Diagram dapat digunakan untuk menggambarkan:

* Alur kerja.
* Proses bisnis.
* Proses login.
* Proses pengajuan.
* Proses validasi.
* Proses evaluasi.
* Proses persetujuan.

Activity Diagram lebih detail dibandingkan Use Case Diagram.

Use Case menjawab:

> "User dapat melakukan apa?"

Activity Diagram menjawab:

> "Bagaimana proses tersebut dilakukan?"

---

## 6.2. Struktur Dasar

Gunakan pola:

```text
Start
  ↓
Aktivitas
  ↓
Decision
  ↓
Aktivitas
  ↓
End
```

---

## 6.3. Initial Node

Menunjukkan awal proses.

```text
●
```

---

## 6.4. Activity

Menunjukkan aktivitas yang dilakukan.

Gunakan kata kerja.

Contoh:

```text
Membuka Halaman Login
Memasukkan NIK
Memasukkan Password
Memvalidasi Data
Menyimpan Data
Mengirim Notifikasi
```

---

## 6.5. Decision

Digunakan untuk percabangan kondisi.

Contoh:

```text
        Data Valid?
        /       \
      Ya        Tidak
      ↓           ↓
  Simpan Data   Tampilkan Error
```

Gunakan kondisi yang jelas.

Contoh:

```text
[Data Valid]
[Data Tidak Valid]
```

Hindari:

```text
[Ya]
[Tidak]
```

jika konteks decision tidak jelas.

---

## 6.6. Fork dan Join

Gunakan Fork apabila satu aktivitas menghasilkan beberapa aktivitas paralel.

Contoh:

```text
             Proses Pengajuan
                    |
                  Fork
                /     \
               ↓       ↓
        Validasi Data  Upload Dokumen
               \       /
                  Join
                    ↓
             Pengajuan Diproses
```

Gunakan Join untuk menggabungkan kembali aktivitas paralel.

---

## 6.7. Swimlane

Gunakan Swimlane untuk menunjukkan siapa yang bertanggung jawab terhadap aktivitas.

Contoh:

```text
+-------------+------------------+----------------+
|   Pengguna  |     Sistem       |    Pimpinan    |
+-------------+------------------+----------------+
| Ajukan Data |                  |                |
|-------------| Validasi Data    |                |
|             | Kirim Notifikasi |                |
|             |------------------| Terima Notif   |
|             |                  | Validasi       |
+-------------+------------------+----------------+
```

Swimlane sangat direkomendasikan untuk proses yang melibatkan banyak actor atau role.

---

## 6.8. Aturan Activity Diagram

* Selalu memiliki Start.
* Memiliki End apabila proses telah selesai.
* Gunakan arah alur yang konsisten.
* Gunakan kata kerja pada aktivitas.
* Decision harus memiliki kondisi yang jelas.
* Setiap cabang harus memiliki tujuan.
* Hindari garis yang saling berpotongan.
* Gunakan Swimlane jika terdapat banyak pihak.
* Gunakan Fork/Join hanya jika benar-benar ada proses paralel.
* Jangan mencampurkan terlalu banyak proses dalam satu diagram.

---

# 7. CLASS DIAGRAM

## 7.1. Tujuan

Class Diagram digunakan untuk menggambarkan struktur sistem berorientasi objek.

Class Diagram dapat menunjukkan:

* Class.
* Attribute.
* Method.
* Visibility.
* Relationship.
* Inheritance.
* Association.
* Multiplicity.

Class Diagram merupakan diagram struktur sehingga fokus utamanya adalah bagaimana objek atau class dalam sistem disusun dan saling berhubungan. Class umumnya direpresentasikan dengan bagian nama class, atribut, dan method.

---

## 7.2. Struktur Class

Gunakan format:

```text
+----------------------------+
| NamaClass                  |
+----------------------------+
| - attribute1               |
| - attribute2               |
+----------------------------+
| + method1()                |
| + method2()                |
+----------------------------+
```

---

## 7.3. Visibility

Gunakan:

```text
+ Public
- Private
# Protected
~ Package
```

Contoh:

```text
User
--------------------
- id
- name
- password
--------------------
+ login()
+ logout()
```

---

## 7.4. Attribute

Attribute menggambarkan data yang dimiliki class.

Contoh:

```text
User
----------------
- id
- nik
- name
- password
- role_id
```

Jika diperlukan, tambahkan tipe data:

```text
- id: int
- name: string
- created_at: datetime
```

---

## 7.5. Method

Method menggambarkan perilaku atau operasi class.

Contoh:

```text
+ login()
+ logout()
+ updateProfile()
```

Method sebaiknya mencerminkan tanggung jawab class.

---

# 8. RELATIONSHIP CLASS DIAGRAM

## 8.1. Association

Menunjukkan hubungan antar class.

Contoh:

```text
User -------- Profile
```

---

## 8.2. Multiplicity

Gunakan:

```text
1       Tepat satu
0..1    Nol atau satu
*       Banyak
0..*    Nol atau banyak
1..*    Satu atau banyak
```

Contoh:

```text
User 1 -------- 1 Profile
```

Artinya:

Satu User memiliki satu Profile.

Contoh:

```text
User 1 -------- 0..* Cooperation
```

Artinya:

Satu User dapat memiliki nol atau banyak data Cooperation.

---

## 8.3. Generalization / Inheritance

Gunakan untuk hubungan:

```text
is-a
```

Contoh:

```text
          User
           ▲
           |
    +------+------+
    |             |
  Admin       Pimpinan
```

Artinya:

Admin dan Pimpinan merupakan turunan dari User.

Gunakan inheritance hanya jika hubungan "adalah sebuah" benar-benar berlaku.

---

## 8.4. Aggregation

Gunakan apabila suatu objek memiliki objek lain, tetapi objek yang dimiliki masih dapat berdiri sendiri.

Konsep:

```text
has-a
```

---

## 8.5. Composition

Gunakan apabila objek bagian sangat bergantung pada objek utama.

Jika objek utama dihapus, objek bagian juga ikut hilang.

Contoh konsep:

```text
Order ◆-------- OrderItem
```

---

# 9. KONSISTENSI ANTAR DIAGRAM

Ini merupakan salah satu standar terpenting.

Setiap diagram harus saling berhubungan.

Contoh:

### Use Case

```text
Pimpinan
    ↓
Validasi Pengajuan
```

Maka Activity Diagram harus memiliki proses:

```text
Pimpinan
    ↓
Membuka Pengajuan
    ↓
Memeriksa Data
    ↓
Memvalidasi Pengajuan
```

Kemudian Sequence Diagram harus menggambarkan komunikasi yang sesuai:

```text
Pimpinan
   ↓
Halaman Validasi
   ↓
Controller
   ↓
Service
   ↓
Database
```

Class Diagram harus memiliki class yang mendukung proses tersebut.

Contoh:

```text
Pengajuan
Evaluasi
User
Notifikasi
```

Jangan sampai:

```text
Use Case:
Validasi Pengajuan

Activity:
Validasi Pengajuan

Sequence:
Evaluasi Kerja Sama

Class:
ApprovalRequest
```

jika istilah-istilah tersebut sebenarnya mengacu pada satu proses yang sama.

Gunakan satu istilah yang konsisten.

---

# 10. ATURAN TRACEABILITY

Setiap fitur utama harus dapat ditelusuri dari kebutuhan hingga implementasi.

Gunakan pola:

```text
Requirement
    ↓
Use Case
    ↓
Activity
    ↓
Sequence
    ↓
Class
    ↓
Implementation
```

Contoh:

```text
Requirement:
Pimpinan dapat memvalidasi pengajuan.

Use Case:
Validasi Pengajuan.

Activity:
Pimpinan membuka pengajuan
→ Memeriksa data
→ Memberikan keputusan
→ Sistem menyimpan hasil.

Sequence:
Pimpinan
→ Validation Page
→ Validation Controller
→ Cooperation Service
→ Database.

Class:
Pimpinan
Pengajuan
Evaluasi
Notifikasi
```

Dengan cara ini, setiap bagian sistem memiliki hubungan yang jelas.

---

# 11. STANDAR PEMECAHAN DIAGRAM

Jika diagram terlalu kompleks, jangan memaksakan semuanya dalam satu diagram.

Contoh:

```text
Use Case Diagram
├── Use Case Umum
├── Use Case Admin
├── Use Case Pimpinan
├── Use Case Jurusan
└── Use Case Unit Kerja
```

Activity Diagram:

```text
Activity
├── Login
├── Pengajuan Kerja Sama
├── Validasi Pengajuan
├── Evaluasi
├── Perpanjangan
└── Laporan
```

Class Diagram:

```text
Class Diagram
├── Authentication
├── User Management
├── Cooperation
├── Evaluation
└── Reporting
```

Pecah diagram berdasarkan domain atau fitur apabila diagram utama sudah sulit dibaca.

---

# 12. STANDAR DESAIN VISUAL

Gunakan prinsip berikut:

## Layout

* Gunakan jarak antar elemen yang konsisten.
* Sejajarkan elemen secara horizontal atau vertikal.
* Gunakan grid jika tersedia.
* Gunakan ukuran elemen yang konsisten.

## Garis

* Hindari garis yang bersilangan.
* Gunakan jenis garis sesuai makna relasi.
* Pastikan arah panah jelas.
* Jangan menggunakan garis dekoratif yang tidak memiliki makna.

## Teks

* Gunakan font yang mudah dibaca.
* Jangan menggunakan terlalu banyak variasi font.
* Gunakan ukuran teks yang konsisten.
* Hindari teks terlalu panjang di dalam elemen diagram.

## Warna

Warna boleh digunakan sebagai alat bantu visual, tetapi jangan menjadikan warna sebagai satu-satunya pembeda makna.

Contoh:

```text
Actor        → Warna A
System       → Warna B
External     → Warna C
```

Jika diagram dicetak hitam putih, diagram tetap harus dapat dipahami.

---

# 13. STANDAR VALIDASI UML

Sebelum diagram dinyatakan selesai, lakukan pemeriksaan berikut.

## Checklist Umum

* [ ] Semua actor sudah teridentifikasi.
* [ ] Semua fitur utama sudah memiliki Use Case.
* [ ] Nama Use Case menggunakan istilah yang konsisten.
* [ ] System Boundary sudah jelas.
* [ ] Tidak ada actor yang tidak digunakan.
* [ ] Tidak ada Use Case yang tidak memiliki tujuan.
* [ ] Activity Diagram memiliki Start.
* [ ] Activity Diagram memiliki alur yang jelas.
* [ ] Decision memiliki kondisi yang jelas.
* [ ] Semua cabang Decision memiliki tujuan.
* [ ] Class memiliki atribut yang relevan.
* [ ] Method sesuai dengan tanggung jawab class.
* [ ] Relationship antar class sudah benar.
* [ ] Multiplicity sudah sesuai.
* [ ] Tidak ada garis yang tidak perlu.
* [ ] Tidak ada elemen yang tumpang tindih.
* [ ] Diagram dapat dibaca tanpa penjelasan tambahan.

---

# 14. CHECKLIST KONSISTENSI ANTAR DIAGRAM

Pastikan:

* [ ] Actor di Use Case sesuai dengan role pengguna sistem.
* [ ] Use Case memiliki Activity Diagram jika prosesnya kompleks.
* [ ] Aktivitas pada Activity Diagram sesuai dengan Use Case.
* [ ] Actor pada Sequence Diagram sesuai dengan Use Case.
* [ ] Method pada Sequence Diagram tersedia pada Class Diagram yang relevan.
* [ ] Object pada Sequence Diagram memiliki class yang sesuai.
* [ ] Status pada State Diagram sesuai dengan status yang digunakan sistem.
* [ ] Relationship pada Class Diagram sesuai dengan struktur domain sistem.
* [ ] Istilah bisnis konsisten di seluruh diagram.

Prinsip penting:

> Jika sebuah operasi digunakan dalam Sequence Diagram, operasi tersebut sebaiknya dapat ditelusuri ke class yang bertanggung jawab dalam Class Diagram.

Diagram harus memberikan gambaran sistem yang konsisten dan informatif, bukan berdiri sendiri-sendiri tanpa hubungan.

---

# 15. WORKFLOW PEMBUATAN DIAGRAM UML

Gunakan workflow berikut dalam setiap pengembangan fitur.

## Tahap 1 — Analisis Kebutuhan

Identifikasi:

```text
Siapa?
Melakukan apa?
Kapan?
Mengapa?
Data apa?
Hasilnya apa?
```

---

## Tahap 2 — Buat Use Case

Tentukan:

```text
Actor
↓
Use Case
↓
Relationship
```

---

## Tahap 3 — Buat Activity

Ambil setiap proses penting dari Use Case.

Kemudian uraikan:

```text
Start
↓
Input
↓
Process
↓
Validation
↓
Decision
↓
Output
↓
End
```

---

## Tahap 4 — Buat Sequence

Tentukan siapa yang berkomunikasi.

Contoh:

```text
Actor
↓
UI
↓
Controller
↓
Service
↓
Repository
↓
Database
```

Sesuaikan dengan arsitektur aplikasi yang sebenarnya.

---

## Tahap 5 — Buat Class Diagram

Identifikasi:

```text
Entity
↓
Attribute
↓
Method
↓
Relationship
↓
Multiplicity
```

---

## Tahap 6 — Validasi

Bandingkan semua diagram.

Pastikan:

```text
Use Case
    ↕
Activity
    ↕
Sequence
    ↕
Class
```

Semua harus menggambarkan proses sistem yang sama dari sudut pandang berbeda.

---

# 16. CONTOH PENERAPAN PADA SISTEM INFORMASI KERJA SAMA

Misalnya terdapat fitur:

```text
Pengajuan Kerja Sama
```

## Use Case

```text
Jurusan
    |
    └──── (Mengajukan Kerja Sama)

Pimpinan
    |
    └──── (Memvalidasi Pengajuan)
```

---

## Activity

```text
Start
  ↓
Jurusan Mengisi Pengajuan
  ↓
Upload Dokumen
  ↓
Sistem Validasi Data
  ↓
[Data Valid?]
  ├── Tidak → Tampilkan Error
  │             ↓
  │          Perbaiki Data
  │
  └── Ya
       ↓
   Simpan Pengajuan
       ↓
   Status = Menunggu Evaluasi
       ↓
   Kirim Notifikasi ke Pimpinan
       ↓
   Pimpinan Memvalidasi
       ↓
   [Disetujui?]
      ├── Ya → Status = Disahkan
      │
      └── Tidak → Status = Revisi
                    ↓
                 Kembali ke Jurusan
                    ↓
                   End
```

---

## Class

```text
User
---------------------
- id
- nik
- name
- role
---------------------
+ login()
+ logout()


Cooperation
---------------------
- id
- status_dokumen
- status_berlaku
- created_by
---------------------
+ submit()
+ updateStatus()


Evaluation
---------------------
- id
- cooperation_id
- evaluator_id
- score
- conclusion
---------------------
+ evaluate()


Notification
---------------------
- id
- user_id
- message
- is_read
---------------------
+ send()
+ markAsRead()
```

Hubungan:

```text
User 1 -------- 0..* Cooperation

Cooperation 1 -------- 0..* Evaluation

User 1 -------- 0..* Evaluation

User 1 -------- 0..* Notification
```

---

# 17. ATURAN EMAS DALAM MEMBUAT UML

Gunakan 10 aturan berikut:

1. **Pahami proses bisnis sebelum menggambar.**
2. **Tentukan aktor sebelum menentukan Use Case.**
3. **Gunakan Use Case untuk menggambarkan kebutuhan fungsional.**
4. **Gunakan Activity untuk menggambarkan alur proses.**
5. **Gunakan Sequence untuk menggambarkan komunikasi antar objek.**
6. **Gunakan Class Diagram untuk menggambarkan struktur sistem.**
7. **Gunakan nama yang konsisten di semua diagram.**
8. **Hindari diagram yang terlalu padat dan kompleks.**
9. **Pastikan setiap hubungan memiliki alasan yang jelas.**
10. **Validasi semua diagram secara silang sebelum implementasi.**

---

# 18. Prinsip Akhir

Diagram UML yang baik bukanlah diagram yang paling banyak memiliki simbol.

Diagram UML yang baik adalah diagram yang:

```text
Jelas
  +
Terstruktur
  +
Konsisten
  +
Mudah Dibaca
  +
Sesuai Kebutuhan
  +
Saling Terhubung
  +
Merepresentasikan Sistem yang Sebenarnya
```

Gunakan UML sebagai **blueprint komunikasi dan perancangan sistem**, bukan sekadar dokumentasi formal.

Sebelum membuat diagram, selalu tanyakan:

> "Informasi apa yang ingin saya komunikasikan melalui diagram ini?"

Jika jawabannya adalah:

```text
Siapa menggunakan sistem?
→ Use Case

Bagaimana proses berjalan?
→ Activity

Bagaimana objek berkomunikasi?
→ Sequence

Bagaimana struktur objek sistem?
→ Class

Bagaimana status objek berubah?
→ State

Bagaimana komponen sistem tersusun?
→ Component

Bagaimana sistem ditempatkan pada infrastruktur?
→ Deployment
```

Maka pilih diagram yang tepat dan jangan mencampurkan tujuan beberapa diagram ke dalam satu diagram.

**Standar akhir:**

```text
Requirement
    ↓
Use Case
    ↓
Activity
    ↓
Sequence
    ↓
Class
    ↓
Implementation
```

Setiap tahap harus dapat ditelusuri dan memiliki hubungan logis dengan tahap lainnya. Dengan pendekatan ini, UML dapat menjadi blueprint yang membantu developer dan stakeholder memahami sistem sebelum proses coding dimulai.