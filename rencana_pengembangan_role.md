# Rencana Pengembangan Role Akses Mitra & Program Studi (Prodi)
*Dokumen Rencana dan Opsi Implementasi Sistem Informasi Kerja Sama & Kurikulum Link and Match (WD4)*

Dokumen ini disusun sebagai bahan koordinasi dengan pihak kampus mengenai penambahan hak akses baru untuk **Mitra (DUDIKA)** dan **Program Studi (Prodi)** pada sistem WD4.

---

## 1. Alur & Progres Sistem (System Workflow)
Sistem informasi kerja sama WD4 dirancang secara fleksibel untuk mendukung **dua metode pendekatan kerja sama** (tradisional dan modern) demi mengakomodasi kebutuhan koordinasi internal kampus dan pihak industri:

```mermaid
graph TD
    subgraph Cara A: Pendekatan Tradisional (Offline First)
        A1[Pertemuan & TTD Fisik Offline] --> A2[Inisiator Kampus Input Data & Scan PDF] --> A3[Approval Pimpinan] --> A4[Status: Disahkan / Aktif]
    end

    subgraph Cara B: Pendekatan Modern (Online First)
        B1[Mitra Ajukan Proposal Publik] --> B2[Pimpinan Validasi Awal] --> B3[Inisiator Kampus Buat Draf Kerjasama] --> B4[Mitra Review Draf Online] --> B5[Approval Akhir & TTD Fisik] --> B6[Status: Disahkan / Aktif]
    end
```

> [!NOTE]
## Alur 1
pada langkah B3 kampus belum mengunggah dokumen yang sudah ditandatangani.
Berikut adalah alasan logis dan terstruktur mengapa hal tersebut dilakukan:

1. Menghindari Tanda Tangan Ganda/Sia-Sia
Jika pihak Kampus (Ketua Jurusan/Kepala Pusat/UPA) sudah menandatangani dokumen fisik pada langkah B3, lalu mengunggahnya untuk ditinjau oleh Mitra pada langkah B4:
Apabila di langkah B4 ternyata Mitra meminta revisi / perubahan pasal dalam perjanjian, maka dokumen fisik yang sudah terlanjur ditandatangani oleh kampus tersebut menjadi tidak berlaku (batal).
Kampus harus membuat ulang dokumen, mencetaknya kembali, dan meminta tanda tangan ulang dari Ketua Jurusan/Unit Kerja. Ini akan membuang-buang kertas dan waktu.

2. Apa yang Dilakukan Kampus pada Langkah B3?
Pada langkah B3 (Inisiator Kampus Buat Draf Kerjasama), kampus hanya menginput rancangan perjanjian ke dalam sistem. Kampus dapat:Mengisi data rencana kerja sama di sistem (Nama Mitra, Judul, Tanggal Berlaku, dll.).
Mengunggah file draf mentah (biasanya format Word/PDF kosong yang belum ditandatangani sama sekali) untuk dibaca dan dikomentari oleh Mitra di langkah B4.
Kapan Tanda Tangan Pihak 1 (Kampus) Dilakukan?
Tanda tangan fisik oleh Ketua Jurusan/Kepala Pusat/UPA (Pihak 1) baru dilakukan pada Langkah B5 setelah:
Mitra setuju dengan isi draf secara online (Langkah B4).
Pimpinan memberikan persetujuan final (Langkah B5).
Dokumen final yang bersih diunduh dari sistem untuk kemudian dicetak dan ditandatangani fisik secara berurutan (Bottom-Up).

# Alur 2 (Fast-Track Option)
Bagaimana Jalurnya?
1. Langkah B3 (Pembuatan Draf oleh Kampus):
- Karena kampus tahu Mitra pasti setuju, Humas/Unit Kerja membuat draf menggunakan template standar kampus.
- Kampus langsung mencetak draf tersebut dan meminta tanda tangan fisik Pihak 1 (Ketua Jurusan/Kepala Pusat/UPA).
- Berkas yang sudah ditandatangani Pihak 1 tersebut dipindai (scan) ke PDF, lalu diunggah ke sistem pada langkah B3.

2. Langkah B4 (Review & TTD oleh Mitra):
- Mitra login, mengunduh file PDF draf tersebut (yang isinya sudah ditandatangani kampus).
- Mitra langsung mencetak, menandatanganinya dari sisi mereka (Pihak 2), lalu men-scan dan mengunggah kembali file PDF yang sudah bertandatangan lengkap (kedua belah pihak) ke sistem.

3. Langkah B5 & B6 (Persetujuan Akhir & Aktivasi):
Pimpinan memvalidasi, memberikan persetujuan akhir, dan status dokumen berubah menjadi Disahkan / Aktif.


> **Inisiator Kampus / Entitas Penginput Data** mencakup: **Humas, Jurusan, Pusat, dan UPA**. Semuanya memiliki alur proses (Cara A dan Cara B) yang **sama persis**. Perbedaannya hanya terletak pada cakupan/unit kerja masing-masing saat menginput data.

### Cara A: Pendekatan Tradisional (Offline First / Arsip Internal)
Digunakan ketika dokumen kerja sama **sudah selesai ditandatangani secara fisik** di luar sistem sebelum dimasukkan ke dalam sistem WD4.

1. **Pertemuan & Penandatanganan Offline**: Kampus dan Mitra bertemu secara langsung untuk menyepakati isi kontrak, mencetak, dan menandatangani dokumen kerja sama fisik (MoU/MoA/SPK) dengan meterai.
2. **Pencatatan & Scan PDF (Humas/Unit Kerja)**: Berkas fisik yang sudah ditandatangani di-scan menjadi file PDF. Humas/Unit Kerja/Jurusan menginput data kerja sama ke sistem WD4 dan mengunggah file PDF tersebut.
3. **Persetujuan Pimpinan (Validasi)**: Pimpinan kampus memvalidasi keselarasan entri data dengan dokumen fisik yang diunggah.
4. **Selesai**: Status dokumen di sistem langsung diatur menjadi **Disahkan / Aktif**. *Pada cara ini, proses review online oleh Mitra di sistem dilewati.*

---

### Cara B: Pendekatan Modern (Online First / Pengajuan Digital)
Digunakan ketika calon mitra mengajukan kerja sama secara digital, atau draf perjanjian (MoU/MoA/SPK) perlu didiskusikan secara online terlebih dahulu sebelum dicetak dan ditandatangani secara fisik.

1. **Pengajuan Awal (Mitra)**: Calon mitra mengisi formulir "Pengajuan Kerja Sama Baru" di halaman welcome publik sistem.
2. **Validasi Awal (Pimpinan)**: Pimpinan menyetujui proposal pengajuan kemitraan tersebut di sistem.
3. **Pembuatan Draf (Humas/Unit Kerja)**: Data proposal masuk ke dashboard Humas/Unit Kerja. Mereka menyusun draf perjanjian digital di sistem (status dokumen: `Draft`).
4. **Review Draf (Mitra)**: Pihak Mitra login ke sistem untuk meninjau draf dokumen kerja sama tersebut secara online (**Review Industri**), lalu memberikan persetujuan atau catatan revisi secara digital.
5. **Persetujuan Akhir (Pimpinan)**: Setelah disepakati bersama secara online oleh Mitra, Humas/Unit mengirim draf ke Pimpinan (status: `Menunggu Evaluasi`) untuk mendapatkan persetujuan akhir.
6. **Tanda Tangan & Aktivasi**: Dokumen final dicetak, ditandatangani secara fisik oleh kedua belah pihak, kemudian diunggah kembali ke sistem untuk mengaktifkan status dokumen menjadi **Disahkan / Aktif**.

---

## 2. Pembagian Tugas & Wewenang Peran (RBAC)

### A. Role Program Studi (Prodi)
Berperan sebagai pelaksana teknis akademik. Wewenang dan fitur yang diperlukan meliputi:
* **Pengajuan Proposal Kurikulum**: Mengisi form pengajuan bantuan kurikulum *Link and Match* bersama industri.
* **Matriks Pemetaan Kompetensi**: Mengakses ruang kerja digital untuk mencocokkan Capaian Pembelajaran Lulusan (CPL) dengan standar kompetensi dari DUDIKA.
* **Input *Implementation Arrangement* (IA)**: Mengunggah dan mendata dokumen kerja sama di tingkat operasional/teknis.
* **Pencatatan Kegiatan Kampus Merdeka**: Mendaftarkan mahasiswa ke dalam 8 bentuk kegiatan luar kampus.
* **Kalkulator Konversi SKS**: Mengonversi skor kinerja dari industri menjadi nilai SKS akademik resmi.

### B. Role Mitra (DUDIKA)
Berperan sebagai kolaborator industri eksternal. Wewenang dan fitur yang diperlukan meliputi:
* **Review Draf Perjanjian**: Menyetujui atau memberikan catatan revisi pada draf MoU/MoA/SPK yang melibatkan instansi mereka (khusus pada *Cara B*).
* **Review Kurikulum**: Meninjau draf kurikulum bersama program studi.
* **Portal Penilaian**: Menginput nilai evaluasi kinerja mahasiswa magang menggunakan rubrik penilaian online.
* **Monitoring Masa Berlaku**: Melihat status keaktifan dokumen kerja sama mereka (*Aktif*, *Kadaluarsa*, *Dalam Perpanjangan*).

---

## 3. Opsi Mekanisme Pembuatan Akun Mitra (Bahan Diskusi Kampus)
Berikut adalah dua pilihan mekanisme yang dapat diterapkan untuk proses pendaftaran akun Mitra:

### Opsi A: Pembuatan Akun Otomatis Setelah Pengajuan Disetujui (Sangat Direkomendasikan)
* **Cara Kerja**:
  1. Calon mitra mengisi form pengajuan kerja sama baru secara publik di landing page.
  2. Pimpinan memeriksa berkas pengajuan tersebut di dashboard pimpinan.
  3. Saat Pimpinan memilih **"Setujui"**, sistem (atau Admin) membuatkan akun user baru di tabel `users` dengan role `mitra` (menggunakan **Email Penanggung Jawab** sebagai email login dan password yang di-generate otomatis).
  4. Sistem otomatis mengirim email notifikasi berisi informasi login dan link untuk mengganti password ke mitra.
* **Kelebihan**:
  * **Sangat Aman**: Mencegah akun spam/fake dari publik luar yang mendaftar sembarangan. Database hanya berisi akun mitra yang resmi disetujui.
  * **Efisien**: Calon mitra hanya perlu mengisi data sekali di form pengajuan, tidak perlu daftar akun manual di awal.
* **Kekurangan**:
  * Membutuhkan integrasi sistem pengiriman email (SMTP/Mailgun) atau WhatsApp API agar informasi login terkirim dengan baik kepada mitra.

```mermaid
graph TD
    subgraph 1. Landing Page Publik
        LP1[Calon Mitra Akses Landing Page] --> LP2[Isi Form Pengajuan Kerja Sama Baru]
        LP2 --> LP3[Submit Proposal Pengajuan]
    end

    subgraph 2. Dashboard Pimpinan
        LP3 --> DP1[Pimpinan Terima Notifikasi Pengajuan Baru]
        DP1 --> DP2{Keputusan Pimpinan?}
        DP2 -- Tolak --> DP3[Sistem Kirim Notifikasi Penolakan ke Calon Mitra]
        DP2 -- Setujui --> DP4[Pimpinan Klik Tombol 'Setujui']
    end

    subgraph 3. Backend Automation / Admin Delegation
        DP4 --> BE1[Sistem Auto-Generate Record Users Baru]
        BE1 --> BE2[Role: 'mitra' | Username/Email: Email Penanggung Jawab]
        BE2 --> BE3[Relasikan user_id dengan mitra_id Baru]
        BE3 --> BE4[Kirim Email Akses Login & Link Reset Password]
    end

    subgraph 4. Akses Mitra Baru
        BE4 --> M1[Mitra Terima Email Akses]
        M1 --> M2[Klik Link Reset Password & Set Password Baru]
        M2 --> M3[Mitra Login ke Dashboard Mitra WD4]
    end
```

#### *Prinsip Tata Kelola Peran (Role Governance: Pimpinan vs Admin vs Sistem Backend)*
Dari sudut pandang *Software Engineering*, pemisahan wewenang antara Pimpinan dan Admin diatur dengan prinsip **Separation of Concerns**:
1. **Role Pimpinan (Executive Decision Maker)**: Pimpinan bertindak khusus sebagai pengambil keputusan strategis. Pimpinan **TIDAK dibebani tugas administrasi teknis** (seperti mengetik password atau menginput akun). Pimpinan cukup mengeklik **"Setujui"** atau **"Tolak"**.
2. **Sistem (Backend Automation Service)**: Saat Pimpinan klik "Setujui", **sistem di belakang layar (backend) yang secara otomatis membuatkan akun user baru** di database dan mengirim email akses ke Mitra. Pimpinan tidak perlu melakukan langkah tambahan apa pun.
3. **Role Admin (System Administrator & Support)**: Admin tidak perlu membuat akun satu-per-satu dari awal untuk mitra baru yang disetujui. Admin bertugas sebagai **pengawas (monitoring)**, mengelola hak akses, menangani *reset password* jika mitra lupa/minta bantuan, atau menonaktifkan akun jika ada masalah teknis.

*(Alternatif Delegasi)*: Jika kampus menginginkan adanya verifikasi email oleh Admin sebelum akun aktif, saat Pimpinan klik "Setujui", data masuk ke daftar *Pending Account* milik **Admin**. Admin cukup mengeklik tombol **"Verifikasi & Kirim Akses"** untuk merilis akun tersebut ke Mitra.

#### *Solusi Software Engineering: Penanganan Pengajuan Baru Bagi Mitra yang Sudah Memiliki Akun*
Agar mitra yang sudah terdaftar tidak membuat akun ganda/duplikat saat mengajukan kerja sama kedua atau ketiga:
1. **Fitur "+ Ajukan Kerja Sama Baru" di Dashboard Mitra**:
   * Disediakan form khusus di dalam dashboard Mitra setelah login.
   * Data identitas perusahaan (Nama, Alamat, Telp, Website, Penandatangan) **otomatis terisi (auto-fill)** dari data profil mereka. Mitra **hanya perlu mengisi rencana kerja sama baru** (Judul, Tujuan, Ruang Lingkup, dan Unit Tujuan).
   * Pengajuan ini langsung terhubung ke `mitra_id` yang sudah ada tanpa membuat akun/user baru.
2. **Proteksi & Pengecekan Otomatis pada Landing Page Publik**:
   * Jika pengguna yang membuka form publik di landing page **sudah dalam posisi login**, sistem otomatis mengalihkan (redirect) ke form pengajuan di **Dashboard Mitra**.
   * Jika belum login tetapi memasukkan **Email Instansi yang sudah terdaftar**, sistem menampilkan notifikasi ramah: *"Email instansi Anda sudah terdaftar di sistem. Silakan [Login di Sini] untuk membuat pengajuan baru dengan mudah dari dashboard Anda."*

```mermaid
graph TD
    subgraph Skenario A: Pengajuan via Landing Page Publik
        A1[Mitra Buka Form Pengajuan di Landing Page] --> A2{Status User Saat Ini?}
        A2 -- Sudah Login --> A3[Auto-Redirect ke Dashboard Mitra]
        A2 -- Belum Login --> A4[Mitra Input Email Instansi]
        A4 --> A5{Email Sudah Terdaftar di Sistem?}
        A5 -- Ya --> A6[Notifikasi: 'Email Terdaftar! Silakan Login']
        A6 --> A7[Mitra Klik Link Login & Masuk Dashboard]
        A5 -- Tidak --> A8[Lanjutkan Form Pengajuan Mitra Baru]
    end

    subgraph Skenario B: Pengajuan via Dashboard Mitra (Mitra Terdaftar)
        B1[Mitra Login di Dashboard Mitra] --> B2[Klik Tombol '+ Ajukan Kerja Sama Baru']
        B2 --> B3[Form Pengajuan Terbuka - Profile Data Auto-fill]
        B3 --> B4[Mitra Hanya Isi Detail Kerja Sama Baru]
        B4 --> B5[Submit Pengajuan Baru]
        B5 --> B6[Sistem Connect Pengajuan Baru ke mitra_id Eksisting]
        B6 --> B7[Pengajuan Terkirim ke Pimpinan Tanpa Duplikasi Akun]
    end
```

#### *Solusi Penanganan Akun untuk Mitra Lama / Eksisting (Dashboard Admin)*
Mitra lama yang data perusahaan dan dokumen kerjasamanya sudah aktif di sistem **TIDAK BOLEH diminta mengisi formulir pengajuan ulang dari nol**. Penanganan akun mitra eksisting diatur di Dashboard Admin sebagai berikut:

1. **Filter Status Akun Mitra di Dashboard Admin**:
   * Pada menu kelola data mitra ([MitraController](file:///c:/laragon/www/wd4/app/Http/Controllers/Admin/MitraController.php)), sistem menampilkan status/filter jelas untuk membedakan antara **"Mitra Sudah Memiliki Akun"** dan **"Mitra Belum Memiliki Akun"**.
2. **Fitur "Kirim Akses Login" (Pembuatan & Pengiriman Akun)**:
   * Untuk daftar mitra yang **Belum Memiliki Akun**, disediakan tombol **"Kirim Akses Login"** (bisa dilakukan secara individu per mitra atau secara massal/batch).
   * Ketika tombol ini diklik oleh Admin, sistem secara otomatis:
     a. Membuatkan record pengguna di tabel `users` dengan `role` = `mitra` dan password yang di-generate aman.
     b. Menghubungkan `user_id` baru tersebut ke `mitra_id` yang sudah ada (sehingga seluruh dokumen lama yang sudah aktif langsung terhubung).
     c. Mengirimkan email notifikasi berisi rincian login dan link aktivasi/reset password ke email mitra tersebut.
3. **Klaim Akun Mandiri via "Lupa Password"**:
   * Sebagai alternatif, mitra lama juga dapat memasukkan email instansi mereka di halaman Lupa Password untuk menerima link aktivasi akun secara mandiri.

```mermaid
graph TD
    subgraph Jalur 1: Tindakan Admin (Dashboard Admin)
        AD1[Admin Akses Menu Kelola Mitra] --> AD2[Filter: Status Akun 'Belum Punya Akun']
        AD2 --> AD3[Admin Klik 'Kirim Akses Login' Per Mitra / Batch]
        AD3 --> AD4[Sistem Auto-Generate User Role 'mitra']
        AD4 --> AD5[Sistem Connect user_id ke mitra_id Eksisting]
        AD5 --> AD6[Sistem Kirim Email Credentials & Reset Link]
    end

    subgraph Jalur 2: Mandiri / Self-Service (Halaman Lupa Password)
        MT1[Mitra Akses Halaman Lupa Password] --> MT2[Mitra Input Email Instansi Terdaftar]
        MT2 --> MT3{Cek Email di Data Mitra Eksisting}
        MT3 -- Ditemukan --> MT4[Sistem Buat Akun & Kirim Link Activation]
        MT3 -- Tidak Ada --> MT5[Tampilkan Pesan Error: Email Tidak Terdaftar]
        MT4 --> MT6[Mitra Klik Link & Set Password Baru]
    end

    subgraph Hasil Akhir
        AD6 --> FIN[Mitra Login & Otomatis Terhubung dengan Seluruh Dokumen Histori/Lama]
        MT6 --> FIN
    end
```
<!-- ### Opsi B: Pendaftaran Akun Mandiri (Register) di Halaman Login
* **Cara Kerja**:
  1. Di halaman login utama, ditambahkan tombol **"Daftar Akun Mitra"**.
  2. Calon mitra mendaftarkan akun secara mandiri dengan mengisi Nama Perusahaan, Email, dan Password.
  3. Setelah terdaftar, mereka bisa masuk ke dashboard mitra untuk mengisi formulir pengajuan kerja sama baru.
* **Kelebihan**:
  * Alur pendaftaran standar yang umum dipahami pengguna.
* **Kekurangan**:
  * Database berpotensi dipenuhi akun sampah (spam) yang tidak pernah mengirimkan proposal pengajuan kerja sama.
  * Proses pengisian menjadi dua kali kerja (daftar akun dulu, baru mengisi form pengajuan). -->

## 4. Rekomendasi UX Halaman Login

Halaman login utama tetap difokuskan untuk pengguna yang sudah memiliki akun resmi. Untuk menghindari kesan bahwa sistem membuka pendaftaran akun secara bebas, tombol atau link bantuan di halaman login tidak menggunakan teks **"Daftar Akun"**, melainkan diarahkan ke dua kebutuhan yang berbeda:

1. **Ajukan Kerja Sama**:
   * Digunakan oleh calon mitra baru yang belum memiliki akun.
   * Link mengarah ke form publik pengajuan kerja sama baru.
   * Posisi ideal berada sejajar dengan fitur **"Lupa kata sandi?"** sebagai aksi sekunder, bukan tombol utama.

2. **Butuh bantuan akses? Hubungi administrator**:
   * Digunakan untuk kendala akses akun, bukan untuk pendaftaran akun bebas.
   * Posisi ideal berada di bagian bawah form login sebagai teks bantuan kecil setelah tombol **"Masuk Sekarang"**.
   * Desain dibuat tenang dan tidak lebih menonjol daripada tombol login atau link pengajuan kerja sama.

Contoh susunan tampilan login:

```text
[ Email atau NIP ]
[ Kata sandi     ]

Lupa kata sandi?              Ajukan kerja sama

[ Masuk Sekarang ]

Butuh bantuan akses? Hubungi administrator
```

### Fitur Bantuan Akses Login

Fitur **"Butuh bantuan akses? Hubungi administrator"** direkomendasikan sebagai kanal bantuan untuk kasus berikut:

1. **User internal belum dibuatkan akun**
   * User menghubungi admin dengan data nama lengkap, NIP, email, unit kerja, dan jabatan dan role.
   * Admin memverifikasi data internal kampus, membuat akun, menentukan role, lalu mengirim link aktivasi atau reset password.

2. **Mitra belum menerima email akses**
   * Mitra memilih nama perusahaan, email penanggung jawab, kode pengajuan jika ada, dan nama penanggung jawab.
   * Admin mengecek status pengajuan, memastikan akun mitra sudah dibuat, lalu mengirim ulang email akses atau link reset password.

3. **Akun terkunci**
   * User mengirim Email/NIP dan identitas dasar.
   * Admin mengecek status akun, membuka kunci akun, mereset percobaan login gagal, dan bila perlu mewajibkan reset password.

4. **Lupa email yang terdaftar**
   * User mengirim identitas pendukung untuk internal atau nama perusahaan/kode pengajuan untuk mitra.
   * Admin melakukan verifikasi identitas. Sistem tidak menampilkan email lengkap secara publik untuk menjaga keamanan.

5. **Password reset tidak masuk email**
   * User melaporkan Email/NIP dan waktu percobaan reset.
   * Admin mengecek email terdaftar, status pengiriman email, folder spam, dan mengirim ulang link reset bila diperlukan.

6. **Role/dashboard salah setelah login**
   * User melaporkan dashboard yang muncul dan dashboard yang seharusnya.
   * Admin memeriksa role di data user serta relasi profil unit kerja, jurusan, pusat, atau UPA, lalu meminta user logout dan login ulang setelah diperbaiki.

### Rekomendasi Desain Bantuan Akses

* Gunakan teks pendek: **"Butuh bantuan akses? Hubungi administrator"**.
* Hindari teks **"Belum punya akun?"** agar mitra tidak salah mengira bahwa akun dapat dibuat secara mandiri.
* Gunakan gaya link kecil dengan ikon bantuan seperti `help-circle`, `headset`, atau `message-circle`.
* Link dapat diarahkan ke WhatsApp admin, email admin, halaman bantuan, atau form tiket bantuan akses.
* Untuk tahap awal, cukup gunakan kontak admin. Untuk tahap lanjutan, buat form tiket bantuan akses dengan pilihan kendala.

---
## 5. Rekomendasi UX Landing Page
1. **Fitur Pengajuan Baru**: Tetap diletakkan di halaman landing page publik ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) karena calon mitra baru belum memiliki akun untuk masuk ke sistem.
2. **Fitur Perpanjangan**: Diletakkan di dalam dashboard setelah login (atau jika tombol di landing page diklik, diarahkan untuk login terlebih dahulu) untuk menghindari manipulasi perpanjangan data oleh pihak lain yang tidak bertanggung jawab.

---

## 6. Penentuan Tujuan Unit Kerja Sama (Multi-Unit Routing)
Agar Pimpinan mengetahui ke mana arah alur kerja sama dari pengajuan baru yang dikirimkan oleh Mitra, sistem akan dirancang dengan mekanisme berikut:

### A. Pilihan Unit Pelaksana pada Form Pengajuan
Pada form pengisian pengajuan kerja sama baru di landing page, calon mitra akan diberikan pilihan unit yang ingin dituju dalam bentuk **Checkbox (Pilihan Ganda)**:
* `[ ]` Tingkat Institusi (Humas)
* `[ ]` Tingkat Jurusan / Program Studi
* `[ ]` Tingkat Pusat (Unit Penelitian & Pengabdian)
* `[ ]` Tingkat UPA (Unit Pelaksana Akademik)

### B. Dukungan Multi-Unit (Lebih dari Satu)
Sistem **sangat mendukung pemilihan lebih dari satu unit pelaksana**. 
* **Alasan Bisnis**: Sebuah industri sering kali ingin melakukan kerja sama magang (melibatkan Jurusan/Prodi) sekaligus riset kolaborasi (melibatkan Pusat).
* **Struktur Database**: Database WD4 saat ini sudah menggunakan tabel pivot Many-to-Many (`kerjasama_jurusan`, `kerjasama_upa`, `kerjasama_pusat`, `kerjasama_prodi`) yang memungkinkan satu data kerja sama terhubung langsung ke beberapa unit secara bersamaan.

### C. Alur Kerja untuk Pimpinan
1. **Melihat Tujuan**: Pada halaman validasi pengajuan milik Pimpinan, sistem akan menampilkan label unit tujuan yang dicentang oleh Mitra (contoh: *Tujuan: Jurusan Teknik Elektro & Pusat Riset*).
2. **Disetujui & Distribusi Notifikasi**: Saat Pimpinan mengeklik "Setujui", sistem secara otomatis mengirimkan notifikasi tugas penulisan draf ke **semua unit pelaksana** yang dicentang tersebut. Admin dari unit-unit terkait dapat masuk ke dashboard mereka untuk berkolaborasi melengkapi dokumen kerja sama tersebut.

---

## 7. Alur Penandatanganan Dokumen Kerja Sama (Signing Hierarchy)
Untuk menjaga struktur birokrasi dan legalitas kampus yang teratur, urutan penandatanganan dokumen kerja sama diatur berdasarkan tingkatan dokumennya:

### A. Tingkat MoU (Memorandum of Understanding / Nota Kesepahaman)
*Skala kerja sama: Institusi ke Institusi (Politeknik dengan Perusahaan)*
* **Urutan Penandatanganan**:
  1. **Pihak Pertama (Pimpinan Kampus)**: Direktur menandatangani dokumen terlebih dahulu.
  2. **Pihak Kedua (Pimpinan Mitra)**: Direktur Utama/Pimpinan Mitra menandatangani dokumen setelahnya sebagai persetujuan akhir kerja sama kelembagaan.

### B. Tingkat MoA / SPK (Perjanjian Kerja Sama) & IA (Implementation Arrangement)
*Skala kerja sama: Tingkat Pelaksana Teknis (Jurusan/Prodi/Pusat/UPA dengan Divisi Mitra)*
* **Urutan Penandatanganan (Birokrasi Bottom-Up)**:
  1. **Tahap 1: Pihak Kesatu Pelaksana (Kampus)** $\rightarrow$ Ditandatangani oleh Ketua Jurusan, Kepala Pusat, atau Kepala UPA selaku penanggung jawab teknis program dari sisi kampus.
  2. **Tahap 2: Pihak Kedua Pelaksana (Mitra)** $\rightarrow$ Ditandatangani oleh Manager/Kepala Divisi dari pihak Mitra Industri.
  3. **Tahap 3: Mengesahkan/Mengetahui (Pimpinan Kampus)** $\rightarrow$ Ditandatangani terakhir oleh Direktur/Wakil Direktur Bidang Kerja Sama (Pimpinan) sebagai pengesahan resmi secara kelembagaan. Dokumen dinyatakan sah di sistem setelah Pimpinan menandatangani.

### C. Implementasi Teknis Fitur Penandatanganan pada Sistem

#### 1) Jika Menggunakan Cara A (Pencatatan Offline / Arsip)
Karena penandatanganan fisik sudah selesai dilakukan di luar sistem:
* **Form Input (Humas/Unit)**: Sistem menyediakan kolom untuk menginput Nama & Jabatan para penandatangan (Pihak 1, Pihak 2, dan Pimpinan yang mengesahkan).
* **Unggah Dokumen**: Admin wajib mengunggah file PDF scan dari dokumen asli yang sudah dibubuhi tanda tangan dan cap basah lengkap.
* **Pengesahan**: Pimpinan hanya perlu memvalidasi kesesuaian data input dengan file PDF tersebut, lalu mengeklik tombol **"Sahkan"** (Status langsung berubah menjadi `Disahkan`).

#### 2) Jika Menggunakan Cara B (Pengajuan Online / Digital First)
Karena proses pembuatan dokumen berjalan dari draf digital di dalam sistem:
* **Penyusunan Draf (Humas/Unit)**: Saat menyusun draf, Humas/Unit menginput rancangan Nama & Jabatan penandatangan.
* **Review online (Mitra)**: Mitra menyetujui isi draf dan susunan penandatangan dari pihak mereka.
* **Persetujuan Pimpinan**: Pimpinan menyetujui draf final di dashboard (Status berubah menjadi `Menunggu Tanda Tangan`).
* **Penandatanganan Fisik**: Sistem menyediakan tombol untuk mengunduh draf final (PDF/Word). Dokumen dicetak, lalu ditandatangani fisik bertahap secara berurutan (*Bottom-Up*):
  * *Langkah 1*: TTD Ketua Jurusan/Pusat/UPA (Pihak 1).
  * *Langkah 2*: TTD Perwakilan Mitra (Pihak 2).
  * *Langkah 3*: TTD Direktur/Wadir IV (Pimpinan Kampus - Mengesahkan).
* **Aktivasi Dokumen**: Setelah ditandatangani lengkap secara fisik, Humas/Unit mengunggah hasil scan PDF dokumen tersebut ke sistem. Status dokumen otomatis berubah menjadi `Disahkan / Aktif`.