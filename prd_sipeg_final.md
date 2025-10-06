# Product Requirements Document (PRD): Sistem Informasi Kepegawaian (SIMPEG) Diskominfo Palembang

## 1. Pendahuluan

### 1.1. Latar Belakang
[cite_start]Saat ini, proses pengelolaan administrasi kepegawaian di Dinas Komunikasi dan Informatika (Diskominfo) Kota Palembang masih menghadapi sejumlah tantangan[cite: 1, 4]. [cite_start]Proses seperti pendataan pegawai, pengelolaan cuti, pemantauan riwayat kenaikan pangkat, dan terutama pengelolaan perjalanan dinas masih sering dilakukan secara manual atau semi-manual[cite: 5]. [cite_start]Pengelolaan manual ini menyebabkan berbagai masalah, seperti pengajuan cuti yang mendadak sehingga proses administrasi tidak berjalan semestinya [cite: 6][cite_start], serta proses perjalanan dinas yang panjang dan tidak terdokumentasi secara sistematis[cite: 6]. [cite_start]Hal ini berisiko menimbulkan *human error*, redundansi data, kehilangan dokumen, dan keterlambatan dalam pencarian informasi serta penyusunan laporan strategis[cite: 8]. [cite_start]Untuk mengatasi masalah tersebut, diperlukan sebuah sistem informasi terpusat yang dapat mengotomatisasi dan mengintegrasikan seluruh proses manajemen kepegawaian[cite: 9].

### 1.2. Visi Produk
[cite_start]Menjadi platform digital yang terpusat, andal, dan mudah digunakan untuk mengelola seluruh siklus administrasi kepegawaian di lingkungan Diskominfo Kota Palembang, sehingga meningkatkan efisiensi, transparansi, dan mendukung pengambilan keputusan berbasis data[cite: 11].

### 1.3. Tujuan & Sasaran
* [cite_start]**Tujuan Utama**: Mengembangkan Sistem Informasi Kepegawaian (SIMPEG) berbasis web menggunakan metodologi *Extreme Programming* (XP) untuk Diskominfo Kota Palembang[cite: 13].
* **Sasaran (Objectives)**:
    1.  [cite_start]Mendigitalkan dan memusatkan data master pegawai, termasuk data pribadi, riwayat pendidikan, keluarga, dan jabatan[cite: 15].
    2.  [cite_start]Mengotomatisasi alur pengajuan dan persetujuan cuti secara online[cite: 16].
    3.  [cite_start]Mengotomatisasi alur penugasan, persetujuan, dan pelaporan perjalanan dinas secara terstruktur dan efisien[cite: 17].
    4.  [cite_start]Menyediakan fitur untuk melacak riwayat kenaikan pangkat dan golongan secara sistematis[cite: 18].
    5.  [cite_start]Menghasilkan laporan kepegawaian secara otomatis untuk keperluan evaluasi dan perencanaan pimpinan[cite: 19].

## 2. Pengguna Sistem (User Personas)
[cite_start]Sistem ini akan digunakan oleh empat jenis pengguna utama dengan hak akses yang berbeda[cite: 21]:

* **Administrator (Admin Kepegawaian)**
    * [cite_start]**Tugas**: Mengelola seluruh data master (pegawai, unit kerja, jabatan, golongan), mengelola akun pengguna, memverifikasi data, dan menghasilkan laporan keseluruhan[cite: 23].
    * [cite_start]**Kebutuhan**: Memiliki akses penuh ke semua fitur untuk memastikan integritas dan kelengkapan data[cite: 24].

* **Pimpinan (Kepala Dinas)**
    * [cite_start]**Tugas**: Memantau data pegawai, memberikan persetujuan untuk pengajuan cuti, menugaskan pegawai untuk perjalanan dinas, mengesahkan surat tugas, dan melihat laporan rekapitulasi untuk pengambilan keputusan[cite: 26].
    * [cite_start]**Kebutuhan**: Akses untuk melakukan persetujuan, melihat dasbor statistik, dan memonitor aktivitas pegawai[cite: 27].

* **Admin Keuangan**
    * [cite_start]**Tugas**: Menerima, melakukan verifikasi, dan memberikan persetujuan atas laporan perjalanan dinas yang diajukan oleh pegawai[cite: 29].
    * [cite_start]**Kebutuhan**: Akses khusus untuk memvalidasi laporan perjalanan dinas dan memastikan pertanggungjawaban sesuai[cite: 30].

* **Pegawai (Staf)**
    * [cite_start]**Tugas**: Melihat dan memperbarui data pribadi (setelah verifikasi admin), mengajukan cuti, menerima penugasan perjalanan dinas, dan membuat (mengunggah) laporan perjalanan dinas secara digital[cite: 32].
    * [cite_start]**Kebutuhan**: Akses terbatas untuk mengelola informasi dan pengajuan yang berkaitan dengan dirinya sendiri[cite: 33].

## 3. Fitur dan Kebutuhan (User Stories)

### Epic 1: Manajemen Data Induk Pegawai
* [cite_start]**Sebagai Admin,** saya ingin dapat menambah, mengubah, dan menonaktifkan data pegawai (pribadi, keluarga, pendidikan) agar database kepegawaian selalu akurat[cite: 36].
* [cite_start]**Sebagai Pegawai,** saya ingin dapat melihat profil data diri saya agar saya bisa memastikan data yang tersimpan sudah benar[cite: 37].

### Epic 2: Manajemen Cuti Online
* [cite_start]**Sebagai Pegawai,** saya ingin mengajukan cuti secara online dengan memilih tanggal dan jenis cuti agar prosesnya lebih cepat dan terdokumentasi[cite: 39].
* [cite_start]**Sebagai Pimpinan,** saya ingin menerima notifikasi pengajuan cuti dan dapat menyetujui atau menolaknya melalui sistem agar proses persetujuan menjadi efisien[cite: 40].
* [cite_start]**Sebagai Admin,** saya ingin dapat melihat rekapitulasi dan sisa cuti seluruh pegawai agar bisa memonitor kuota cuti tahunan[cite: 41].
* **Sebagai Pegawai,** saat mengajukan cuti tahunan, saya ingin sistem secara otomatis menampilkan dan menghitung total sisa cuti saya yang mencakup sisa cuti dari tahun berjalan (N), satu tahun sebelumnya (N-1), dan dua tahun sebelumnya (N-2).

#### **Catatan Aturan Bisnis (Business Rules) untuk Cuti:**
* Sistem harus secara otomatis menghitung total sisa cuti tahunan yang tersedia bagi seorang pegawai.
* Perhitungan ini mengakumulasikan sisa cuti dari tahun berjalan (N), sisa cuti dari satu tahun sebelumnya (N-1), dan sisa cuti dari dua tahun sebelumnya (N-2).
* Sisa cuti yang berumur lebih dari dua tahun (hangus) tidak akan dihitung.
* Saat pegawai mengambil cuti, sistem akan mengurangi jatah cuti dari tahun yang paling lama terlebih dahulu (dimulai dari N-2, lalu N-1, baru N).

### Epic 3: Manajemen Perjalanan Dinas
* [cite_start]**Sebagai Pimpinan,** saya ingin dapat menugaskan pegawai untuk perjalanan dinas dan menerbitkan Surat Perintah Tugas (SPT) secara digital agar prosesnya cepat dan resmi[cite: 43].
* [cite_start]**Sebagai Pegawai,** saya ingin menerima notifikasi penugasan perjalanan dinas dan dapat mengunggah laporan hasil kegiatan setelah selesai agar pertanggungjawaban terdokumentasi dengan baik[cite: 44].
* [cite_start]**Sebagai Admin Keuangan,** saya ingin menerima notifikasi laporan perjalanan dinas yang masuk dan melakukan verifikasi laporan tersebut melalui sistem untuk memastikan kesesuaiannya[cite: 45].
* [cite_start]**Sebagai Admin,** saya ingin dapat melihat rekapitulasi seluruh perjalanan dinas yang telah dilakukan untuk kebutuhan pelaporan instansi[cite: 46].

### Epic 4: Manajemen Karir (Pangkat & Jabatan)
* [cite_start]**Sebagai Admin,** saya ingin mencatat dan mengelola riwayat kenaikan pangkat dan mutasi jabatan setiap pegawai agar jejak karir pegawai terdokumentasi dengan baik[cite: 48].
* [cite_start]**Sebagai Pegawai,** saya ingin dapat melihat riwayat pangkat dan jabatan saya agar saya mengetahui perjalanan karir saya di dinas[cite: 49].

### Epic 5: Pelaporan dan Dasbor
* [cite_start]**Sebagai Pimpinan,** saya ingin melihat dasbor yang menampilkan statistik kunci (misal: jumlah pegawai yang sedang cuti, pegawai yang paling sering melakukan perjalanan dinas) agar dapat membuat perencanaan SDM yang lebih baik[cite: 51].
* [cite_start]**Sebagai Admin,** saya ingin dapat mencetak laporan daftar urut kepangkatan (DUK) dan laporan rekapitulasi pegawai agar dapat memenuhi kebutuhan laporan fisik[cite: 52].

### Epic 6: Manajemen Pengguna
* [cite_start]**Sebagai Admin,** saya ingin dapat mengelola akun pengguna (membuat akun, reset password, mengatur hak akses) agar keamanan sistem tetap terjaga[cite: 54].

## 4. Kebutuhan Non-Fungsional
* [cite_start]**Keamanan**: Sistem harus menggunakan sistem login dengan hak akses berbasis peran (*Role-Based Access Control*)[cite: 56].
* [cite_start]**Usability**: Antarmuka harus intuitif, responsif (dapat diakses di berbagai ukuran layar), dan mudah dipelajari oleh pengguna[cite: 57].
* [cite_start]**Kinerja**: Waktu muat halaman tidak lebih dari 3 detik untuk menjaga kenyamanan pengguna[cite: 58].
* [cite_start]**Platform**: Aplikasi berbasis web yang dapat diakses melalui browser modern seperti Google Chrome atau Mozilla Firefox[cite: 59].

---

## 5. Perencanaan Entity-Relationship Diagram (ERD)

### 5.1. Identifikasi Entitas Utama
* [cite_start]`Pegawai` [cite: 62]
* [cite_start]`Pengguna` [cite: 63]
* [cite_start]`Jabatan` [cite: 64]
* [cite_start]`Golongan` [cite: 65]
* [cite_start]`Unit_Kerja` [cite: 66]
* [cite_start]`Pendidikan` [cite: 67]
* [cite_start]`Keluarga` [cite: 68]
* [cite_start]`Cuti` [cite: 69]
* [cite_start]`Jenis_Cuti` [cite: 70]
* `Sisa_Cuti` **(BARU)**
* [cite_start]`Perjalanan_Dinas` [cite: 71]
* [cite_start]`Laporan_PD` [cite: 72]
* [cite_start]`Riwayat_Pangkat` [cite: 73]
* [cite_start]`Riwayat_Jabatan` [cite: 74]

---

## 6. Desain Entitas dan Kolom untuk SIMPEG Laravel

### Pendahuluan: Konvensi Laravel
* [cite_start]**Primary Key**: Setiap tabel akan memiliki kolom `id` sebagai *Primary Key* auto-increment[cite: 154].
* [cite_start]**Timestamps**: Setiap tabel akan memiliki kolom `created_at` dan `updated_at` secara otomatis[cite: 155].
* [cite_start]**Foreign Keys**: Kolom *foreign key* akan menggunakan format `nama_tabel_singular_id`[cite: 156].
* [cite_start]**Soft Deletes**: Untuk data krusial, ditambahkan kolom `deleted_at` untuk fitur *soft delete*[cite: 157].

### Bagian 1: Manajemen Pengguna (Users, Roles, & Permissions)

**1. [cite_start]Tabel `users`** [cite: 161]

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `pegawai_id` | `unsignedBigInteger` | Foreign Key ke tabel `pegawai`. Relasi 1-ke-1. |
| `username` | `string, unique` | Username untuk login. |
| `email` | `string, unique` | Email pengguna. |
| `password` | `string` | Hash password. |
| `remember_token`| `string, nullable`| Token untuk fitur "Ingat Saya". |
| `timestamps` | `timestamp` | `created_at` dan `updated_at`. |
| `softDeletes` | `timestamp` | Kolom `deleted_at` untuk soft delete. |

**2. Tabel `roles`, `permissions`, dan Pivotnya**
[cite_start]Menggunakan package `spatie/laravel-permission` untuk tabel `roles` [cite: 165][cite_start], `permissions` [cite: 169][cite_start], dan tabel pivot `role_user` [cite: 174] [cite_start]serta `permission_role`[cite: 175].

### Bagian 2: Entitas Inti Kepegawaian

**3. [cite_start]Tabel `pegawai`** [cite: 177]

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `NIP` | `string, unique` | Nomor Induk Pegawai. |
| `nama_lengkap` | `string` | Nama lengkap pegawai. |
| ... (kolom data diri lainnya) | ... | ... |
| `jabatan_id` | `unsignedBigInteger` | Foreign Key ke tabel `jabatan`. |
| `golongan_id` | `unsignedBigInteger` | Foreign Key ke tabel `golongan`. |
| `unit_kerja_id`| `unsignedBigInteger` | Foreign Key ke tabel `unit_kerja`. |
| `timestamps` | `timestamp` | |
| `softDeletes` | `timestamp` | |

**4. [cite_start]Tabel `jabatan`, `golongan`, `unit_kerja` (Tabel Master)** [cite: 181]

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `nama` | `string` | Contoh: "Pengelola Sistem", "III/a", "Bidang E-Government". |
| `deskripsi` | `text, nullable`| |
| `timestamps` | `timestamp` | |

### Bagian 3: Entitas Riwayat & Data Pendukung Pegawai

[cite_start](Tabel `pendidikan` [cite: 189][cite_start], `keluarga` [cite: 194][cite_start], `riwayat_pangkat` [cite: 199][cite_start], dan `riwayat_jabatan` [cite: 204] tetap sama seperti sebelumnya)

### Bagian 4: Entitas Fungsional (Proses Bisnis)

**5. [cite_start]Tabel `jenis_cuti`** [cite: 210]

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `nama` | `string` | 'Cuti Tahunan', 'Cuti Sakit', 'Cuti Besar', dll. |
| `timestamps`| `timestamp` | |

**6. [cite_start]Tabel `cuti`** [cite: 214]

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `pegawai_id` | `unsignedBigInteger` | Foreign Key ke `pegawai` (yang mengajukan). |
| `jenis_cuti_id`| `unsignedBigInteger` | Foreign Key ke `jenis_cuti`. |
| `pimpinan_approver_id` | `unsignedBigInteger, nullable` | Foreign Key ke `users` (yang menyetujui). |
| ... (kolom detail cuti lainnya) | ... | ... |
| `status_persetujuan`| `enum(...)` | 'Diajukan', 'Disetujui', 'Ditolak'. |
| `timestamps` | `timestamp` | |

**7. Tabel `sisa_cuti` (BARU)**
* **Eloquent Model**: `SisaCuti`
* **Fungsi**: Menyimpan data sisa cuti tahunan untuk setiap pegawai per tahunnya.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigIncrements` | Primary Key |
| `pegawai_id` | `unsignedBigInteger` | Foreign Key ke tabel `pegawai`. |
| `tahun` | `year` | Tahun berlakunya jatah cuti (e.g., 2025, 2024). |
| `jatah_cuti` | `integer` | Jumlah total jatah cuti pada tahun tersebut (e.g., 12). |
| `sisa_cuti` | `integer` | Sisa cuti yang belum terpakai pada tahun tersebut. |
| `timestamps` | `timestamp` | `created_at` dan `updated_at`. |

[cite_start]**(Tabel `perjalanan_dinas` [cite: 218][cite_start], `pegawai_perjalanan_dinas` [cite: 222][cite_start], dan `laporan_pd` [cite: 225] tetap sama seperti sebelumnya)**

### Catatan Implementasi di Laravel
1.  [cite_start]**Manajemen Role & Permission**: Sangat disarankan untuk menggunakan package populer seperti `spatie/laravel-permission`[cite: 230].
2.  [cite_start]**Migrations & Models**: Buat *migration* [cite: 232] [cite_start]dan *model* [cite: 233] untuk setiap tabel, definisikan relasi di dalamnya.
3.  [cite_start]**Seeders**: Buat *seeders* untuk data master agar database memiliki data awal yang konsisten[cite: 235].
4.  [cite_start]**Authorization**: Gunakan fitur *Policies* atau *Gates* dari Laravel untuk memproteksi setiap aksi berdasarkan peran atau izin pengguna[cite: 236].
5.  **Scheduled Job**: Buat sebuah *job* terjadwal di Laravel untuk mengelola data `sisa_cuti` setiap awal tahun, yaitu menghapus data yang hangus dan menambahkan jatah cuti baru.