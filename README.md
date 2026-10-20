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

## Usulan Pengembangan Integrasi PMB

Bagian ini merupakan **rancangan pengembangan**, bukan daftar fitur PMB yang sudah tersedia pada aplikasi saat ini. Integrasi dapat dilakukan bertahap dengan layanan IAM dan API yang sudah ada.

### Pembayaran dan penetapan biaya

- Integrasikan payment gateway seperti Midtrans, Xendit, atau Tripay untuk membuat Virtual Account, QRIS, atau pembayaran melalui gerai ritel. Status pembayaran diperbarui melalui webhook yang diverifikasi.
- Pertahankan alur transfer manual sebagai fallback, dengan unggah bukti pembayaran dan dashboard verifikasi keuangan untuk menyetujui atau menolak pembayaran.
- Kaitkan tagihan dengan periode, jalur pendaftaran, dan program studi agar penetapan biaya dapat dikelola secara konsisten.

### Penilaian RPL

- Sediakan matriks pemetaan bukti pengalaman kerja atau sertifikat ke mata kuliah dan SKS pada kurikulum.
- Pisahkan akses asesor RPL dari role lain, serta sediakan rubrik penilaian dan catatan untuk setiap bukti kompetensi.
- Simpan keputusan konversi agar dapat ditinjau dan diaudit.

### Verifikasi identitas

- Validasi format NIK dan data tanggal lahir secara lokal sebagai pemeriksaan awal; validasi format tidak membuktikan identitas atau keaslian dokumen.
- Pertimbangkan OCR melalui Tesseract atau layanan cloud untuk membantu ekstraksi data KTP, dengan konfirmasi pendaftar sebelum data disimpan.
- Jika diperlukan, tambahkan verifikasi selfie/liveness dan pencocokan foto melalui penyedia yang sesuai. Proses ini perlu memperhatikan persetujuan pengguna, perlindungan data pribadi, dan retensi berkas.

### Referral dan notifikasi

- Sediakan dashboard referral untuk melihat progres pendaftar dari pendaftaran hingga registrasi ulang serta, bila berlaku, status komisi.
- Integrasikan gateway WhatsApp untuk OTP dan notifikasi status seleksi, tagihan, serta komisi. Pengiriman perlu menggunakan persetujuan penerima dan template pesan yang sesuai.

### Provisioning mahasiswa dan Neo Feeder

- Setelah calon mahasiswa diterima dan menyelesaikan registrasi ulang, siapkan provisioning akun secara terkontrol ke IAM/SSO kampus, dengan mekanisme retry dan pencatatan hasil sinkronisasi.
- Buat pemetaan data PMB ke format Web Service PDDikti Neo Feeder. Validasi data dan proses sinkronisasi batch sebelum mengirim data, serta simpan status dan respons untuk rekonsiliasi.

## Pengujian

Jalankan test aplikasi dengan:

```bash
php artisan test
```
