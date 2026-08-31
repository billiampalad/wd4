# TASK: Design & Implement Menu Data Kerjasama Prodi

Anda adalah **UI/UX Designer dan Senior Frontend Developer profesional** yang bekerja pada proyek Laravel ini. Gunakan kemampuan analisis Anda untuk memahami terlebih dahulu struktur, arsitektur, flow bisnis, database, serta pola UI/UX yang sudah digunakan di proyek sebelum membuat perubahan apa pun.

## 1. REFERENSI SKILL

Sebelum mengerjakan task, **WAJIB membaca dan memahami** kedua file berikut:

* `frontend-design/SKILL.md`
* `skill-creator/SKILL.md`

Ikuti prinsip, standar, dan aturan yang terdapat di kedua file tersebut selama proses analisis, perencanaan, dan implementasi.

---

# 2. REFERENSI HALAMAN UTAMA

Saya ingin membuat halaman yang berasal dari menu pada:

`resources/views/auth/prodi.blade.php`

Perhatikan bagian:

`prodi.blade.php#L110-113`

Bagian tersebut merupakan menu/fitur yang akan diarahkan ke halaman baru.

Buat halaman tujuan pada:

`resources/views/auth/layout/mitra/prodi/dkerjasama.blade.php`

**Catatan:** sebelum membuat halaman baru, analisis terlebih dahulu bagaimana menu pada `prodi.blade.php` bekerja, bagaimana routing-nya, dan bagaimana pola halaman lain dalam proyek menangani fitur yang serupa.

---

# 3. ANALISIS SISTEM TERLEBIH DAHULU

Sebelum membuat UI atau kode apa pun, **WAJIB memahami sistem yang sudah ada** dengan membaca:

* `pengembangan-sistem/analysis-use-case.md`
* `pengembangan-sistem/analysis-flowchart.md`
* `pengembangan-sistem/analysis-dfd.md`
* `pengembangan-sistem/analysis-erd.md`

Tujuan analisis adalah memahami:

* Role dan hak akses pengguna.
* Flow proses kerjasama.
* Hubungan Prodi dengan Jurusan, Mitra, dan data kerjasama.
* Data apa saja yang boleh dilihat oleh Prodi.
* Data apa saja yang tidak boleh ditampilkan.
* Relasi antar tabel/database.
* Flow input, pengajuan, evaluasi, validasi, dan monitoring.
* Status data kerjasama.
* Relasi data yang sudah digunakan oleh sistem.
* Pola navigasi dan struktur halaman yang sudah ada.

**Jangan membuat asumsi terhadap struktur database atau business logic jika informasi tersebut sebenarnya sudah tersedia di file proyek.**

Jika membutuhkan informasi tambahan dari source code, cari dan analisis file yang relevan sebelum menentukan implementasi.

---

# 4. REFERENSI CSS

Untuk halaman baru:

`mitra/prodi/dkerjasama.blade.php`

gunakan sistem styling yang **sudah digunakan pada halaman Prodi**.

Referensi CSS terdapat pada:

`prodi.blade.php#L18`

Identifikasi file CSS yang digunakan dari bagian tersebut.

**Jangan membuat sistem styling yang bertabrakan dengan CSS yang sudah ada.**

Jika memungkinkan, gunakan class/style pattern yang sudah digunakan pada proyek agar tampilan halaman baru tetap konsisten.

Jika membutuhkan CSS tambahan, tambahkan dengan struktur class yang jelas dan tidak menyebabkan konflik dengan halaman lain.

---

# 5. REFERENSI JAVASCRIPT

Gunakan JavaScript yang sudah digunakan oleh halaman Prodi sebagai referensi.

Referensi:

`prodi.blade.php#L205`

Analisis file JavaScript yang digunakan pada bagian tersebut sebelum membuat JavaScript baru.

Jika terdapat fungsi yang sudah dapat digunakan kembali, **reuse daripada membuat ulang**.

Jika membutuhkan fungsi tambahan, pastikan tidak menyebabkan:

* duplicate event listener,
* konflik variable,
* konflik selector,
* error JavaScript,
* atau mengganggu halaman Prodi lainnya.

---

# 6. TUJUAN HALAMAN

Halaman:

`mitra/prodi/dkerjasama.blade.php`

merupakan halaman untuk menampilkan **data kerjasama yang relevan bagi pengguna Prodi**.

Tampilan harus mengikuti pola desain sistem yang sudah ada, tetapi tetap dibuat lebih modern, clean, profesional, dan mudah digunakan.

Jangan hanya membuat halaman yang terlihat bagus secara visual. Pastikan desain mengikuti **business flow dan data flow sistem yang sebenarnya**.

---

# 7. UI/UX YANG DIHARAPKAN

Buat desain dengan prinsip:

* Modern Dashboard
* Clean UI
* Professional
* Minimalist tetapi informatif
* Consistent dengan halaman Prodi yang sudah ada
* Responsive
* User-friendly
* Mudah melakukan scanning data

Perhatikan secara khusus:

### Header

Buat header halaman yang jelas berisi:

* Judul halaman.
* Deskripsi singkat.
* Breadcrumb jika sesuai dengan struktur navigasi proyek.

### Summary

Jika sesuai dengan data sistem, tampilkan informasi ringkas seperti:

* Total Kerjasama.
* Kerjasama Aktif.
* Kerjasama Dalam Perpanjangan.
* Kerjasama Kadaluarsa.
* Status lainnya yang relevan.

**Jangan menambahkan statistik yang tidak memiliki dasar dari database atau business logic proyek.**

### Data Table

Buat tabel kerjasama yang informatif dan mudah dibaca.

Gunakan kolom yang memang relevan dengan kebutuhan Prodi berdasarkan hasil analisis ERD, DFD, use case, dan flowchart.

Contoh kemungkinan informasi:

* No.
* Judul Kerjasama.
* Mitra.
* Jenis Kerjasama.
* Unit Pelaksana jika relevan.
* Periode/Masa Berlaku.
* Status Kerjasama.
* Status Dokumen.
* Aksi.

Namun **tentukan kolom final berdasarkan hasil analisis sistem**, bukan hanya mengikuti contoh tersebut.

---

# 8. STATUS DATA

Jika sistem memiliki status kerjasama, gunakan status yang benar-benar digunakan oleh database.

Tampilkan status menggunakan badge yang mudah dibedakan secara visual.

Contohnya jika memang sesuai dengan enum/database:

* Aktif → hijau
* Dalam Perpanjangan → orange
* Kadaluarsa → merah
* Tidak Aktif → abu-abu
* Proses → ungu

Jangan membuat status baru yang tidak ada dalam sistem.

---

# 9. FILTER & INTERACTION

Jika berdasarkan analisis sistem halaman ini membutuhkan filtering, gunakan filter yang relevan dengan data Prodi.

Contohnya:

* Search.
* Jenis kerjasama.
* Status.
* Mitra.
* Periode/tahun.

Buat filtering:

* cepat,
* responsif,
* tidak menyebabkan layout bergeser,
* dan tidak mengganggu tabel.

Jika data filtering membutuhkan AJAX/fetch, gunakan pola yang sudah digunakan oleh proyek.

---

# 10. RESPONSIVE DESIGN

Pastikan halaman bekerja dengan baik pada:

* Desktop.
* Laptop.
* Tablet.
* Mobile.

Pada layar kecil, tabel tidak boleh merusak layout halaman.

Jika tabel membutuhkan horizontal scrolling, buat area scroll khusus pada tabel tanpa menyebabkan seluruh halaman ikut bergeser.

---

# 11. KONSISTENSI DENGAN SISTEM

Sangat penting:

**Jangan membuat halaman ini terlihat seperti aplikasi yang berbeda dari proyek utama.**

Pertahankan:

* Font.
* Color palette.
* Button style.
* Card style.
* Border radius.
* Shadow.
* Spacing.
* Icon style.
* Table style.
* Dark mode jika proyek sudah mendukung dark mode.
* Responsive behavior.

Namun, lakukan refinement agar hasil akhirnya terlihat lebih profesional.

---

# 12. KETENTUAN IMPLEMENTASI

Sebelum melakukan perubahan:

1. Analisis struktur proyek.
2. Baca seluruh referensi dokumentasi yang diberikan.
3. Analisis `prodi.blade.php`.
4. Identifikasi CSS yang digunakan.
5. Identifikasi JavaScript yang digunakan.
6. Cari halaman `dkerjasama` lain yang sudah ada pada role Unit/Jurusan/Pusat/UPA jika tersedia.
7. Bandingkan pola UI/UX yang sudah digunakan.
8. Analisis controller, route, model, relationship, dan query yang relevan.
9. Pastikan data yang akan ditampilkan memang tersedia pada database.

**Jangan mengubah database atau business logic yang sudah berjalan tanpa alasan yang jelas.**

Prioritaskan reuse terhadap komponen, style, query pattern, dan fungsi yang sudah tersedia.

---

# 13. OUTPUT TAHAP PERTAMA — PLAN ONLY

**Untuk tahap pertama, JANGAN membuat atau mengubah kode apa pun.**

Saya hanya ingin Anda membuat **Implementation Plan** terlebih dahulu.

Plan harus menjelaskan:

### A. System Analysis

* Apa yang Anda pahami mengenai fitur ini.
* Role Prodi dan hak aksesnya.
* Data yang akan digunakan.
* Relasi database yang relevan.

### B. Existing Code Analysis

Jelaskan file apa saja yang perlu digunakan/dimodifikasi, termasuk:

* Blade.
* Controller.
* Route.
* Model.
* CSS.
* JavaScript.

### C. UI/UX Plan

Jelaskan struktur halaman:

* Header.
* Summary.
* Filter.
* Table.
* Status badge.
* Action.
* Empty state.
* Loading state.
* Responsive behavior.

### D. Data Flow

Jelaskan alur:

`User Prodi → Route → Controller → Model/Query → Database → Blade → UI`

### E. Interaction Flow

Jelaskan bagaimana:

* Search.
* Filter.
* Sorting.
* Pagination.
* Detail.
* Action button.

akan bekerja jika memang diperlukan.

### F. File Changes

Buat daftar file yang:

* Akan dibuat.
* Akan dimodifikasi.
* Tidak perlu dimodifikasi.

### G. Risk & Compatibility

Identifikasi kemungkinan:

* CSS conflict.
* JS conflict.
* Query issue.
* Authorization issue.
* Responsive issue.
* Dark mode issue.
* Duplicate functionality.

### H. Final UI Concept

Berikan gambaran struktur halaman secara sederhana, misalnya:

`Page Header`
↓
`Summary Cards`
↓
`Filter/Search`
↓
`Data Table`
↓
`Pagination`

---

# 14. ATURAN PENTING

**Jangan langsung melakukan coding.**

Pada tahap ini saya hanya ingin melihat **PLAN terlebih dahulu**.

Setelah Anda selesai melakukan analisis, tampilkan:

**“IMPLEMENTATION PLAN — READY FOR REVIEW”**

kemudian berikan seluruh plan secara terstruktur.

**Tunggu persetujuan saya sebelum mulai membuat atau mengubah kode.**
