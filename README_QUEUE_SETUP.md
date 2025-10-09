# Queue Worker Setup untuk SIMPEG

## Konfigurasi Queue

### 1. Environment Configuration

Pastikan konfigurasi queue di `.env` sudah benar:

```env
# Queue configuration
QUEUE_CONNECTION=database

# Email configuration (production)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=simpeg@diskominfo.palembang.go.id
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Jalankan Migration

Pastikan tabel jobs dan failed jobs sudah ada:

```bash
php artisan queue:table
php artisan migrate
```

### 3. Setup Queue Worker

#### Untuk Development:

```bash
# Terminal 1: Queue worker
php artisan queue:work

# Terminal 2: Server development
php artisan serve
```

#### Untuk Production (Linux/Mac):

```bash
# Setup Supervisor
sudo apt install supervisor

# Buat supervisor config
sudo nano /etc/supervisor/conf.d/simpeg-worker.conf
```

Isi konfigurasi supervisor:

```ini
[program:simpeg-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/simpeg-worker.log
stopwaitsecs=3600
```

Aktifkan supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simpeg-worker:*
```

#### Untuk Production (Windows):

Buat scheduler task atau gunakan Windows Service:

```bash
# Jalankan sebagai background process
start /B php artisan queue:work --daemon
```

### 4. Monitoring Queue

#### Cek Status Queue:

```bash
# Lihat jumlah jobs antrian
php artisan queue:monitor

# Lihat jobs yang gagal
php artisan queue:failed

# Restart queue worker
php artisan queue:restart
```

#### Dashboard Monitoring:

Install Laravel Horizon untuk monitoring:

```bash
composer require laravel/horizon

# Publish horizon config
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

# Setup dashboard route
# Tambahkan di config/horizon.php
```

### 5. Configuration Optimasi

#### Untuk High Traffic:

```env
# Increase queue connections
QUEUE_CONNECTION=redis

# Multiple workers
# Jalankan dengan parameter
php artisan queue:work --queue=high,default,low --sleep=1 --tries=3
```

#### Failed Jobs Handling:

```bash
# Retry semua failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### 6. Testing Queue

#### Test Notifikasi Email:

```php
// Test via Artisan command
php artisan tinker

// Jalankan test
$user = App\Models\User::first();
$user->notify(new App\Notifications\CutiApproved(App\Models\Cuti::first()));
```

#### Test Database Notifications:

```php
// Cek notifications
$user = App\Models\User::first();
$notifications = $user->notifications;
dd($notifications);
```

### 7. Troubleshooting

#### Common Issues:

1. **Queue tidak jalan**:
   ```bash
   php artisan queue:listen
   ```

2. **Email tidak terkirim**:
   - Cek konfigurasi mail
   - Cek log: `storage/logs/laravel.log`
   - Test SMTP connection

3. **Jobs gagal**:
   ```bash
   php artisan queue:failed --id=1
   ```

4. **Memory issues**:
   ```bash
   php artisan queue:work --memory=512
   ```

### 8. Best Practices

#### Production Setup:

1. Gunakan Redis untuk queue connection (jika memungkinkan)
2. Setup monitoring untuk queue status
3. Konfigurasi alerts untuk failed jobs
4. Setup backup untuk failed jobs table
5. Gunakan multiple workers untuk high traffic

#### Security:

1. Jangan expose queue status ke public
2. Validasi semua jobs yang di-queue
3. Limit retries untuk infinite loops
4. Monitor failed jobs untuk security issues

---

## Quick Start untuk SIMPEG

```bash
# 1. Setup database queue
php artisan queue:table
php artisan migrate

# 2. Test queue worker
php artisan queue:work --timeout=60

# 3. Setup supervisor (production)
sudo nano /etc/supervisor/conf.d/simpeg-worker.conf

# 4. Aktifkan worker
sudo supervisorctl start simpeg-worker:*
```
