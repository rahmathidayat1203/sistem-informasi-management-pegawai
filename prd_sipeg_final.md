# Product Requirements Document (PRD): Sistem Informasi Kepegawaian (SIMPEG) Diskominfo Palembang

---

### **1. Pendahuluan**

**1.1. Latar Belakang**

[cite_start]Saat ini, proses pengelolaan administrasi kepegawaian di Dinas Komunikasi dan Informatika (Diskominfo) Kota Palembang masih menghadapi sejumlah tantangan[cite: 4]. [cite_start]Proses seperti pendataan pegawai, pengelolaan cuti, pemantauan riwayat kenaikan pangkat, dan terutama pengelolaan perjalanan dinas masih sering dilakukan secara manual atau semi-manual[cite: 5]. [cite_start]Pengelolaan manual ini menyebabkan berbagai masalah, seperti pengajuan cuti yang mendadak sehingga proses administrasi tidak berjalan semestinya, serta proses perjalanan dinas yang panjang dan tidak terdokumentasi secara sistematis[cite: 6]. [cite_start]Hal ini berisiko menimbulkan *human error*, redundansi data, kehilangan dokumen, dan keterlambatan dalam pencarian informasi serta penyusunan laporan strategis[cite: 7]. [cite_start]Untuk mengatasi masalah tersebut, diperlukan sebuah sistem informasi terpusat yang dapat mengotomatisasi dan mengintegrasikan seluruh proses manajemen kepegawaian[cite: 8].

**1.2. Visi Produk**

[cite_start]Menjadi platform digital yang terpusat, andal, dan mudah digunakan untuk mengelola seluruh siklus administrasi kepegawaian di lingkungan Diskominfo Kota Palembang, sehingga meningkatkan efisiensi, transparansi, dan mendukung pengambilan keputusan berbasis data[cite: 10].

**1.3. Tujuan & Sasaran**

* [cite_start]**Tujuan Utama:** Mengembangkan Sistem Informasi Kepegawaian (SIMPEG) berbasis web menggunakan metodologi *Extreme Programming* (XP) untuk Diskominfo Kota Palembang[cite: 12].
* **Sasaran (Objectives):**
    * [cite_start]Mendigitalkan dan memusatkan data master pegawai, termasuk data pribadi, riwayat pendidikan, keluarga, dan jabatan[cite: 14].
    * [cite_start]Mengotomatisasi alur pengajuan dan persetujuan cuti secara online[cite: 15].
    * [cite_start]Mengotomatisasi alur penugasan, persetujuan, dan pelaporan perjalanan dinas secara terstruktur dan efisien[cite: 16].
    * [cite_start]Menyediakan fitur untuk melacak riwayat kenaikan pangkat dan golongan secara sistematis[cite: 17].
    * [cite_start]Menghasilkan laporan kepegawaian secara otomatis untuk keperluan evaluasi dan perencanaan pimpinan[cite: 18].

---

### **2. Pengguna Sistem (User Personas)**

[cite_start]Sistem ini akan digunakan oleh empat jenis pengguna utama dengan hak akses yang berbeda[cite: 20]:

* **Administrator (Admin Kepegawaian)**
    * [cite_start]**Tugas:** Mengelola seluruh data master (pegawai, unit kerja, jabatan, golongan), mengelola akun pengguna, memverifikasi data, dan menghasilkan laporan keseluruhan[cite: 22].
    * [cite_start]**Kebutuhan:** Memiliki akses penuh ke semua fitur untuk memastikan integritas dan kelengkapan data[cite: 23].
* **Pimpinan (Kepala Dinas)**
    * [cite_start]**Tugas:** Memantau data pegawai, memberikan persetujuan untuk pengajuan cuti, menugaskan pegawai untuk perjalanan dinas, mengesahkan surat tugas, dan melihat laporan rekapitulasi untuk pengambilan keputusan[cite: 25].
    * [cite_start]**Kebutuhan:** Akses untuk melakukan persetujuan, melihat dasbor statistik, dan memonitor aktivitas pegawai[cite: 26].
* **Admin Keuangan**
    * [cite_start]**Tugas:** Menerima, melakukan verifikasi, dan memberikan persetujuan atas laporan perjalanan dinas yang diajukan oleh pegawai[cite: 28].
    * [cite_start]**Kebutuhan:** Akses khusus untuk memvalidasi laporan perjalanan dinas dan memastikan pertanggungjawaban sesuai[cite: 29].
* **Pegawai (Staf)**
    * [cite_start]**Tugas:** Melihat dan memperbarui data pribadi (setelah verifikasi admin), mengajukan cuti, menerima penugasan perjalanan dinas, dan membuat (mengunggah) laporan perjalanan dinas secara digital[cite: 31].
    * [cite_start]**Kebutuhan:** Akses terbatas untuk mengelola informasi dan pengajuan yang berkaitan dengan dirinya sendiri[cite: 32].

---

### **3. Fitur dan Kebutuhan (User Stories)**

**Epic 1: Manajemen Data Induk Pegawai**
* [cite_start]Sebagai Admin, saya ingin dapat menambah, mengubah, dan menonaktifkan data pegawai (pribadi, keluarga, pendidikan) agar database kepegawaian selalu akurat[cite: 35].
* [cite_start]Sebagai Pegawai, saya ingin dapat melihat profil data diri saya agar saya bisa memastikan data yang tersimpan sudah benar[cite: 36].

**Epic 2: Manajemen Cuti Online**
* [cite_start]Sebagai Pegawai, saya ingin mengajukan cuti secara online dengan memilih tanggal dan jenis cuti agar prosesnya lebih cepat dan terdokumentasi[cite: 38].
* [cite_start]Sebagai Pimpinan, saya ingin menerima notifikasi pengajuan cuti dan dapat menyetujui atau menolaknya melalui sistem agar proses persetujuan menjadi efisien[cite: 39].
* **(Baru)** Sebagai **Pegawai**, saya ingin **menerima notifikasi melalui sistem dan email** ketika pengajuan cuti saya telah disetujui atau ditolak, agar saya segera mengetahui status pengajuan saya.
* [cite_start]Sebagai Admin, saya ingin dapat melihat rekapitulasi dan sisa cuti seluruh pegawai agar bisa memonitor kuota cuti tahunan[cite: 40].
* [cite_start]Sebagai Pegawai, saat mengajukan cuti tahunan, saya ingin sistem secara otomatis menampilkan dan menghitung total sisa cuti saya yang mencakup sisa cuti dari tahun berjalan (N), satu tahun sebelumnya (N-1), dan dua tahun sebelumnya (N-2)[cite: 41].

**Epic 3: Manajemen Perjalanan Dinas**
* [cite_start]Sebagai Pimpinan, saya ingin dapat menugaskan pegawai untuk perjalanan dinas dan menerbitkan Surat Perintah Tugas (SPT) secara digital agar prosesnya cepat dan resmi[cite: 48].
* [cite_start]Sebagai Pegawai, saya ingin menerima notifikasi penugasan perjalanan dinas dan dapat mengunggah laporan hasil kegiatan setelah selesai agar pertanggungjawaban terdokumentasi dengan baik[cite: 49].
* [cite_start]Sebagai Admin Keuangan, saya ingin menerima notifikasi laporan perjalanan dinas yang masuk dan melakukan verifikasi laporan tersebut melalui sistem untuk memastikan kesesuaiannya[cite: 50].
* **(Baru)** Sebagai **Pimpinan (Kepala Dinas)**, saya ingin **menerima notifikasi melalui sistem dan email** ketika Admin Keuangan telah selesai memverifikasi laporan perjalanan dinas, agar saya terinformasi mengenai status pertanggungjawaban staf.
* [cite_start]Sebagai Admin, saya ingin dapat melihat rekapitulasi seluruh perjalanan dinas yang telah dilakukan untuk kebutuhan pelaporan instansi[cite: 51].

**Epic 4: Manajemen Karir (Pangkat & Jabatan)**
* [cite_start]Sebagai Admin, saya ingin mencatat dan mengelola riwayat kenaikan pangkat dan mutasi jabatan setiap pegawai agar jejak karir pegawai terdokumentasi dengan baik[cite: 53].
* [cite_start]Sebagai Pegawai, saya ingin dapat melihat riwayat pangkat dan jabatan saya agar saya mengetahui perjalanan karir saya di dinas[cite: 54].

**Epic 5: Pelaporan dan Dasbor**
* [cite_start]Sebagai Pimpinan, saya ingin melihat dasbor yang menampilkan statistik kunci (misal: jumlah pegawai yang sedang cuti, pegawai yang paling sering melakukan perjalanan dinas) agar dapat membuat perencanaan SDM yang lebih baik[cite: 56].
* [cite_start]Sebagai Admin, saya ingin dapat mencetak laporan daftar urut kepangkatan (DUK) dan laporan rekapitulasi pegawai agar dapat memenuhi kebutuhan laporan fisik[cite: 57].

**Epic 6: Manajemen Pengguna**
* [cite_start]Sebagai Admin, saya ingin dapat mengelola akun pengguna (membuat akun, reset password, mengatur hak akses) agar keamanan sistem tetap terjaga[cite: 59].

---

### **4. Kebutuhan Non-Fungsional**

* [cite_start]**Keamanan:** Sistem harus menggunakan sistem login dengan hak akses berbasis peran (*Role-Based Access Control*)[cite: 61].
* [cite_start]**Usability:** Antarmuka harus intuitif, responsif (dapat diakses di berbagai ukuran layar), dan mudah dipelajari oleh pengguna[cite: 62].
* [cite_start]**Kinerja:** Waktu muat halaman tidak lebih dari 3 detik untuk menjaga kenyamanan pengguna[cite: 63].
* [cite_start]**Platform:** Aplikasi berbasis web yang dapat diakses melalui browser modern seperti Google Chrome atau Mozilla Firefox[cite: 64].

---

### **5. Perencanaan Entity-Relationship Diagram (ERD)**

**5.1. Identifikasi Entitas Utama**
* [cite_start]Pegawai [cite: 67]
* [cite_start]Pengguna [cite: 68]
* [cite_start]Jabatan [cite: 69]
* [cite_start]Golongan [cite: 70]
* [cite_start]Unit_Kerja [cite: 71]
* [cite_start]Pendidikan [cite: 72]
* [cite_start]Keluarga [cite: 73]
* [cite_start]Cuti [cite: 74]
* [cite_start]Jenis_Cuti [cite: 75]
* [cite_start]Sisa_Cuti [cite: 76]
* [cite_start]Perjalanan_Dinas [cite: 77]
* [cite_start]Laporan_PD [cite: 78]
* [cite_start]Riwayat_Pangkat [cite: 79]
* [cite_start]Riwayat_Jabatan [cite: 80]

---

### **6. Desain Entitas dan Kolom untuk SIMPEG Laravel**

**Pendahuluan: Konvensi Laravel**
* [cite_start]**Primary Key:** Setiap tabel akan memiliki kolom `id` sebagai *Primary Key* auto-increment[cite: 83].
* [cite_start]**Timestamps:** Setiap tabel akan memiliki kolom `created_at` dan `updated_at` secara otomatis[cite: 84].
* [cite_start]**Foreign Keys:** Kolom *foreign key* akan menggunakan format `nama_tabel_singular_id`[cite: 85].
* [cite_start]**Soft Deletes:** Untuk data krusial, ditambahkan kolom `deleted_at` untuk fitur *soft delete*[cite: 86].

**Bagian 1: Manajemen Pengguna (Users, Roles, & Permissions)**

**1. Tabel `users`**

| Nama Kolom     | Tipe Data          | Keterangan                                      |
| :------------- | :----------------- | :---------------------------------------------- |
| id             | bigIncrements      | [cite_start]Primary Key [cite: 91]                          |
| pegawai_id     | unsignedBigInteger | Foreign Key ke tabel `pegawai`. [cite_start]Relasi 1-ke-1. [cite: 92] |
| username       | string, unique     | [cite_start]Username untuk login. [cite: 93]                      |
| email          | string, unique     | [cite_start]Email pengguna. [cite: 94]                            |
| password       | string             | [cite_start]Hash password. [cite: 95]                             |
| remember_token | string, nullable   | [cite_start]Token untuk fitur "Ingat Saya". [cite: 96]            |
[cite_start]| timestamps     | timestamp          | created_at dan updated_at. [cite: 97]               |
| softDeletes    | timestamp          | [cite_start]Kolom deleted_at untuk soft delete. [cite: 98]        |

**2. Tabel `roles`, `permissions`, dan Pivotnya**
[cite_start]Menggunakan package `spatie/laravel-permission` untuk tabel `roles`, `permissions`, dan tabel pivot `role_user` serta `permission_role`[cite: 100].

**Bagian 2: Entitas Inti Kepegawaian**

**3. Tabel `pegawai`**

| Nama Kolom                    | Tipe Data          | Keterangan                             |
| :---------------------------- | :----------------- | :------------------------------------- |
| id                            | bigIncrements      | [cite_start]Primary Key [cite: 105]                        |
| NIP                           | string, unique     | [cite_start]Nomor Induk Pegawai. [cite: 106]                 |
| nama_lengkap                  | string             | [cite_start]Nama lengkap pegawai. [cite: 107]                |
| ... (kolom data diri lainnya) | ...                | ...                                    |
| jabatan_id                    | unsignedBigInteger | [cite_start]Foreign Key ke tabel `jabatan`. [cite: 109]      |
| golongan_id                   | unsignedBigInteger | [cite_start]Foreign Key ke tabel `golongan`. [cite: 110]     |
| unit_kerja_id                 | unsignedBigInteger | [cite_start]Foreign Key ke tabel `unit_kerja`. [cite: 111]   |
[cite_start]| timestamps                    | timestamp          | [cite: 112]                                    |
[cite_start]| softDeletes                   | timestamp          | [cite: 113]                                    |

**4. Tabel `jabatan`, `golongan`, `unit_kerja` (Tabel Master)**

| Nama Kolom  | Tipe Data     | Keterangan                                                      |
| :---------- | :------------ | :-------------------------------------------------------------- |
| id          | bigIncrements | [cite_start]Primary Key [cite: 117]                                               |
| nama        | string        | [cite_start]Contoh: "Pengelola Sistem", "III/a", "Bidang E-Government". [cite: 118] |
[cite_start]| deskripsi   | text, nullable| [cite: 119]                                                           |
[cite_start]| timestamps  | timestamp     | [cite: 120]                                                           |


**Bagian 4: Entitas Fungsional (Proses Bisnis)**

**5. Tabel `jenis_cuti`**

| Nama Kolom | Tipe Data     | Keterangan                                         |
| :--------- | :------------ | :------------------------------------------------- |
| id         | bigIncrements | [cite_start]Primary Key [cite: 127]                                  |
| nama       | string        | [cite_start]'Cuti Tahunan', 'Cuti Sakit', 'Cuti Besar', dll. [cite: 128] |
[cite_start]| timestamps | timestamp     | [cite: 129]                                                |

**6. Tabel `cuti`**

| Nama Kolom             | Tipe Data                | Keterangan                                     |
| :--------------------- | :----------------------- | :--------------------------------------------- |
| id                     | bigIncrements            | [cite_start]Primary Key [cite: 133]                                |
| pegawai_id             | unsignedBigInteger       | [cite_start]Foreign Key ke pegawai (yang mengajukan). [cite: 134]    |
| jenis_cuti_id          | unsignedBigInteger       | [cite_start]Foreign Key ke `jenis_cuti`. [cite: 135]                 |
| pimpinan_approver_id   | unsignedBigInteger, nullable | [cite_start]Foreign Key ke `users` (yang menyetujui). [cite: 136]      |
| ... (kolom detail cuti lainnya) | ...                      | ...                                            |
| status_persetujuan     | enum(...)                | [cite_start]'Diajukan', 'Disetujui', 'Ditolak'. [cite: 138]        |
[cite_start]| timestamps             | timestamp                | [cite: 139]                                            |

**7. Tabel `sisa_cuti`**

| Nama Kolom  | Tipe Data          | Keterangan                                                 |
| :---------- | :----------------- | :--------------------------------------------------------- |
| id          | bigIncrements      | [cite_start]Primary Key [cite: 143]                                          |
| pegawai_id  | unsignedBigInteger | [cite_start]Foreign Key ke tabel pegawai. [cite: 143]                          |
| tahun       | year               | [cite_start]Tahun berlakunya jatah cuti (e.g., 2025, 2024). [cite: 143]          |
| jatah_cuti  | integer            | [cite_start]Jumlah total jatah cuti pada tahun tersebut (e.g., 12). [cite: 143]    |
| sisa_cuti   | integer            | [cite_start]Sisa cuti yang belum terpakai pada tahun tersebut. [cite: 143]       |
[cite_start]| timestamps  | timestamp          | created_at dan updated_at. [cite: 143]                           |

---

### **7. Catatan Implementasi di Laravel**
* [cite_start]**Manajemen Role & Permission:** Sangat disarankan untuk menggunakan package populer seperti `spatie/laravel-permission`[cite: 146].
* [cite_start]**Migrations & Models:** Buat *migration* dan *model* untuk setiap tabel, definisikan relasi di dalamnya[cite: 147].
* [cite_start]**Seeders:** Buat *seeders* untuk data master agar database memiliki data awal yang konsisten[cite: 148].
* [cite_start]**Authorization:** Gunakan fitur *Policies* atau *Gates* dari Laravel untuk memproteksi setiap aksi berdasarkan peran atau izin pengguna[cite: 149].
* [cite_start]**Scheduled Job:** Buat sebuah *job* terjadwal di Laravel untuk mengelola data `sisa_cuti` setiap awal tahun, yaitu menghapus data yang hangus dan menambahkan jatah cuti baru[cite: 150].