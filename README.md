# PMB XRPL Feeder

Aplikasi Laravel untuk layanan **Identity and Access Management (IAM)** dan integrasi aplikasi kampus. Repositori ini menyediakan pengelolaan pengguna, role dan permission, autentikasi API, serta beberapa modul administrasi aktivitas kampus.

## Fitur

- **Manajemen pengguna** untuk administrasi akun dan kredensial.
- **Role dan permission** berbasis `spatie/laravel-permission`, termasuk role awal untuk kebutuhan kampus.
- **Integrasi API berbasis OAuth2** menggunakan Laravel Passport.
- **Sinkronisasi role SIAKAD** melalui endpoint integrasi.
- **Autentikasi dua faktor** dan verifikasi akun.
- **Modul aktivitas kampus**, termasuk sesi, presensi, laporan, dan tautan pendek.

## Teknologi

- PHP 8.2+
- Laravel 12
- MySQL/MariaDB atau SQLite
- Laravel Passport dan Spatie Laravel Permission
- Node.js dan npm untuk build aset frontend
- Tailwind CSS dan Vite

## Persyaratan

- PHP 8.2 atau lebih baru beserta ekstensi yang dibutuhkan Laravel
- Composer
- Node.js dan npm
- Database yang didukung Laravel, misalnya MySQL/MariaDB atau SQLite

## Instalasi

1. Clone repositori dan masuk ke direktori proyek:

   ```bash
   git clone https://github.com/Dikywahyu/pmbxrplxfeeder.git
   cd pmbxrplxfeeder
   ```

2. Pasang dependensi PHP dan siapkan konfigurasi environment:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Sesuaikan pengaturan koneksi database pada `.env` sebelum menjalankan migrasi. Jika menggunakan SQLite, pastikan berkas database SQLite sudah tersedia.

3. Jalankan migrasi dan seeder role/permission:

   ```bash
   php artisan migrate --seed
   ```

   Seeder ini membuat role dan permission awal. Seeder tidak membuat akun administrator.

4. Pasang dependensi frontend dan build aset:

   ```bash
   npm install
   npm run build
   ```

5. (Opsional) Buat tautan untuk file pada storage:

   ```bash
   php artisan storage:link
   ```

6. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Aplikasi tersedia di `http://localhost:8000`.

## Endpoint API

Endpoint API didefinisikan di `routes/api.php` dan umumnya memiliki prefiks `/api`. Di antaranya:

| Endpoint | Kegunaan |
| --- | --- |
| `POST /api/v1/login-client` | Login untuk client API |
| `GET /api/user-profile` | Mengambil profil pengguna beserta role dan permission |
| `POST /api/v1/logout-client` | Logout client |
| `POST /api/v1/sync-role-siakad` | Sinkronisasi role dari SIAKAD |

Periksa konfigurasi autentikasi dan middleware aplikasi sebelum menghubungkan client. Simpan kredensial dan secret integrasi di environment, bukan di repositori.

## Pengujian

Jalankan test aplikasi dengan:

```bash
php artisan test
```
