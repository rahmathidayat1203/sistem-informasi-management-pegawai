<div align="center">
  <h1>🏛️ Sistem Informasi Kepegawaian (SIMPEG)</h1>
  <p><strong>Diskominfo Kota Palembang</strong></p>
  
  <img src="https://img.shields.io/badge/Laravel-12.0-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-8.0-green" alt="Database">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-purple" alt="Frontend">
</div>

## 📖 Tentang Sistem

Sistem Informasi Kepegawaian (SIMPEG) adalah aplikasi web berbasis Laravel yang dirancang khusus untuk mengelola administrasi kepegawaian di Dinas Komunikasi dan Informatika (Diskominfo) Kota Palembang dengan fitur lengkap sesuai PRD yang telah ditetapkan.

### ✨ Fitur Utama

#### 🔐 Manajemen Pengguna dan Akses
- **Role-Based Access Control (RBAC)** dengan 4 role utama:
  - **Admin Kepegawaian**: Akses penuh ke semua fitur dan master data
  - **Pimpinan**: Persetujuan cuti, penugasan perjalanan dinas, monitoring
  - **Admin Keuangan**: Verifikasi laporan perjalanan dinas dan keuangan
  - **Pegawai**: Self-service data pribadi, pengajuan cuti, monitoring tugas
- **Login Ganda**: Dapat menggunakan email atau username
- **Sistem Notifikasi**: Real-time notifications untuk workflow

#### 📋 Manajemen Data Induk Pegawai
- Master data Pegawai lengkap (pribadi, keluarga, pendidikan)
- Data Karir (riwayat pangkat, riwayat jabatan)
- Master Referensi (jabatan, golongan, unit kerja)
- Upload/download dokumen pendukung
- Self-service profile untuk pegawai

#### 📅 Manajemen Cuti Online
- Pengajuan cuti dengan perhitungan N/N-1/N-2 sisa cuti otomatis
- Workflow persetujuan cuti yang transparan
- Tracking sisa cuti per tahun dengan akumulasi
- Jenis cuti yang lengkap (tahunan, sakit, besar, dll)
- Notifikasi otomatis untuk pimpinan dan pegawai

#### ✈️ Manajemen Perjalanan Dinas
- Penugasan pegawai oleh pimpinan
- Pembuatan Surat Perintah Tugas (SPT) digital
- Upload laporan hasil perjalanan dinas
- Verifikasi oleh Admin Keuangan
- Tracking biaya dan statistik perjalanan

#### 📊 Pelaporan dan Analitik
- **DUK (Daftar Urut Kepangkatan)** lengkap
- Rekapitulasi pegawai dengan filter dinamis
- Statistik cuti dan perjalanan dinas
- Dashboard berbasis role untuk monitoring
- Export laporan (PDF/Excel ready)

#### 🔔 Manajemen Lanjutan
- Scheduled job untuk otomasi maintenance
- Audit trail untuk semua perubahan data
- Soft delete untuk data integrity
- Backup dan restore system data

---

## 🚀 Quick Start

### Prasyarat
- PHP 8.2+
- MySQL 8.0+ atau MariaDB 10.3+
- Composer
- Node.js dan NPM
- Git

### Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd sipeg
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Setup Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

   Aplikasi akan tersedia di `http://localhost:8000`

---

## 👤 Akun Default

| Role | Email | Username | Password |
|------|--------|----------|----------|
| **Super Admin** | `admin@example.com` | `admin` | `password` |
| **Admin Kepegawaian** | `admin@sipeg.test` | `admin` | `password` |
| **Pimpinan** | `pimpinan@sipeg.test` | `pimpinan` | `password` |
| **Admin Keuangan** | `keuangan@sipeg.test` | `keuangan` | `password` |
| **Pegawai** | `pegawai@sipeg.test` | `pegawai` | `password` |

> ⚠️ **Penting**: Segera ubah password default setelah login pertama kali untuk keamanan sistem!

---

## 📁 Struktur Proyek

```
sipeg/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Controllers untuk admin functions
│   │   ├── Auth/            # Authentication controllers
│   │   └── ...             # Other controllers
│   ├── Models/              # Eloquent models
│   ├── Jobs/                # Background jobs
│   └── Notifications/       # Notification classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/          # Admin views
│   │   ├── auth/           # Auth views
│   │   ├── dashboard/      # Dashboard views
│   │   └── ...
│   └── assets/           # CSS, JS, images
├── routes/
│   ├── web.php           # Web routes
│   └── auth.php          # Auth routes
└── tests/               # Test cases
```

---

## 🎯 Penggunaan Berdasarkan Role

### 👨‍💼 **Admin Kepegawaian**
1. Login dengan akun admin
2. Akses dashboard `Dashboard Admin Kepegawaian`
3. **Menu Utama**:
   - Master Data: Pegawai, Jabatan, Golongan, Unit Kerja
   - Laporan: DUK, Rekap Pegawai, Statistik
   - User Management: Buat/edit user dan role
   - Monitoring: Semua aktivitas sistem

### 👨‍💼 **Pimpinan**
1. Login dengan akun pimpinan
2. Akses dashboard `Dashboard Pimpinan`
3. **Menu Utama**:
   - Persetujuan Cuti: Review dan approve/reject
   - Penugasan Perjalanan Dinas: Buat SPT digital
   - Monitoring: Status pegawai dan aktivitas
   - Notifikasi: Alert untuk approval yang pending

### 👩‍💼 **Admin Keuangan**
1. Login dengan akun keuangan
2. Akses dashboard `Dashboard Admin Keuangan`
3. **Menu Utama**:
   - Verifikasi Laporan: Review laporan perjalanan dinas
   - Financial Oversight: Tracking biaya dan anggaran
   - Pembayaran: Status pembayaran dan perkembalian
   - Reporting: Keuangan reports

### 👨‍💼 **Pegawai**
1. Login dengan akun pegawai
2. Akses dashboard `Dashboard Pegawai`
3. **Menu Utama**:
   - Profile: Lihat dan edit data pribadi
   - Cuti: Ajukan, lihat riwayat, sisa cuti
   - Perjalanan Dinas: Lihat tugas dan upload laporan
   - Dokumen: Upload dokumen pendukung

---

## 🔧 Komponen Teknis

### **Backend**
- **Framework**: Laravel 12.0
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Breeze + Spatie Permission
- **Queue**: Redis untuk background jobs
- **Cache**: Redis untuk performance

### **Frontend**
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS dengan DataTables
- **Icons**: Feather Icons / Heroicons
- **Build Tools**: Vite

### **Fitur Optional**
- **Email Notifications**: Laravel Mail
- **File Upload**: Laravel Storage
- **API Documentation**: Laravel Passport (opsional)
- **WebSockets**: Laravel Echo (opsional)

---

## 🧪 Testing

Jalankan test suite untuk memastikan sistem berfungsi dengan baik:

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=AuthenticationTest

# Generate test coverage
php artisan test --coverage
```

Test mencakup:
- Authentication dan authorization
- CRUD operations
- Business logic (sisa cuti, dll)
- API endpoints
- Notification system

---

## 📚 Dokumentasi API

API documentation dapat diakses menggunakan:
- **URL**: `/api/documentation` (jika dinyalakan)
- **Format**: OpenAPI/Swagger
- **Authentication**: Bearer Token

---

## 🔄 Maintenance

### Backup Database
```bash
# Backup database
php artisan db:backup --database=mysql --destination=backups/

# Hapus backup lama (>30 hari)
php artisan backup:clean
```

### Scheduled Jobs
```bash
# Jalankan semua scheduled jobs
php artisan schedule:run

 Queue jobs otomatis:
- Sisa cuti rollover (setiap awal tahun)
- Hapus data lama
- Generate reports periodik
```

### Monitoring
- **Log Files**: `storage/logs/laravel.log`
- **Performance**: Laravel Telescope
- **Error Tracking**: Sentry (jika dikonfigurasi)

---

## 🛠️ Troubleshooting

### Masalah Umum

**1. Error 404 Not Found**
```bash
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**2. Database Connection Error**
- Periksa konfigurasi database di `.env`
- Pastikan MySQL/MariaDB service aktif
- Verifikasi kredensial database

**3. Permission Error**
```bash
# Fix folder permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache
```

**4. Login Issues**
- Periksa apakah user memiliki role yang sesuai
- Verifikasi middleware configuration
- Clear session: `php artisan session:clear`

---

## 🚨 Security

- ✅ **RBAC**: Role-based access control
- ✅ **Input Validation**: Laravel validation rules
- ✅ **CSRF Protection**: Built-in Laravel CSRF
- ✅ **SQL Injection**: Eloquent ORM protection
- ✅ **XSS Protection**: Laravel templating protection
- ✅ **File Upload**: Validasi tipe dan ukuran file

---

## 🛠️ Issue Reporting

Jika menemukan bug atau error:

1. **Check Logs**: `storage/logs/laravel.log`
2. **Enable Debug Mode**: Set `APP_DEBUG=true` di `.env`
3. **Run Tests**: `php artisan test`
4. **Report Issue**: Include:
   - Steps to reproduce
   - Error message
   - Environment details
   - Screenshots (jika perlu)

---

## 📄 Changelog

### v1.0.0 (2024-12-XX)
- ✅ Initial release
- ✅ Complete CRUD operations
- ✅ RBAC implementation
- ✅ Cuti management dengan N/N-1/N-2 tracking
- ✅ Perjalanan dinas workflow
- ✅ Dashboard per role
- ✅ Notification system
- ✅ Advanced reporting
- ✅ Self-service pegawai profile

---

# 💬 Support

Untuk bantuan lebih lanjut:

- 📧 **Email**: r63800@gmail.com
- 📞 **Telepon**: (0711) XXX XXXX
- 🌐 **Website**: diskominfo.palembang.go.id

---

<div align="center">
  <p>Made with ❤️ by <strong>Rahmat Hidayat</strong></p>
  
  <p>© 2024 Sistem Informasi Kepegawaian Diskominfo Palembang. All rights reserved.</p>
</div>
