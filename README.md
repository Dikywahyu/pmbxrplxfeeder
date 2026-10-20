# PMB XRPL Feeder

Aplikasi Laravel untuk Identity and Access Management (IAM) dan administrasi kampus. Modul Penerimaan Mahasiswa Baru (PMB) mencakup pendaftaran, biaya, referral marketing, dan ujian berbasis komputer (CBT).

## Fitur utama

- Manajemen pengguna, role, dan permission.
- Login dengan Google, verifikasi akun, autentikasi dua faktor, dan API berbasis Laravel Passport.
- Modul kegiatan kampus, presensi, laporan, dan tautan pendek.
- PMB: periode, jalur, program studi dengan ID eksternal DIKTI berformat UUID, pendaftaran, pembayaran, dan verifikasi.
- Pengaturan biaya pendaftaran dan SPP untuk beberapa program studi sekaligus.
- Marketing & referral: kode dan tautan referral serta jumlah pendaftar yang tercatat.
- CBT PMB: ujian khusus jalur terpilih, soal pilihan ganda dan esai, gambar pada soal/opsi, lampiran gambar jawaban, nilai otomatis untuk pilihan ganda, dan penilaian manual untuk esai.

## Teknologi dan persyaratan

- PHP 8.2 atau lebih baru beserta ekstensi yang dibutuhkan Laravel
- Composer
- Node.js dan npm
- MySQL/MariaDB atau SQLite
- Laravel 12, Laravel Passport, Spatie Laravel Permission, dan Vite

## Instalasi lokal

1. Clone repositori dan masuk ke folder proyek:

   ```bash
   git clone https://github.com/Dikywahyu/pmbxrplxfeeder.git
   cd pmbxrplxfeeder
   ```

2. Pasang dependensi PHP dan buat konfigurasi lokal:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Di Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp`.

3. Atur `.env` untuk database dan Google OAuth. Variabel yang digunakan untuk Google OAuth adalah `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, dan `GOOGLE_REDIRECT_URI`. Daftarkan URI callback yang sama pada konfigurasi OAuth Google; secara default URI ini mengikuti `${APP_URL}/auth/google/callback`.

   `.env.example` memakai SQLite. Pastikan berkas `database/database.sqlite` tersedia, atau ubah `DB_CONNECTION` dan kredensialnya untuk MySQL/MariaDB. Jangan commit `.env`, client secret, atau kredensial lain ke repositori.

4. Jalankan migrasi dan seeder:

   ```bash
   php artisan migrate --seed
   ```

   Seeder membuat role dan permission kampus serta role PMB. Seeder **tidak membuat akun pengguna atau password default**.

5. Pasang dan build aset frontend:

   ```bash
   npm install
   npm run build
   ```

6. Aktifkan akses URL untuk gambar CBT yang disimpan pada public storage:

   ```bash
   php artisan storage:link
   ```

   Bukti pembayaran pendaftar tetap disimpan pada storage privat.

7. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Buka `http://localhost:8000`.

## Akses awal dan akun administrator

Login aplikasi memakai Google OAuth melalui `/auth/login` atau `/auth/google`. Pengguna baru dibuat saat login Google pertama kali; tidak ada username atau password bawaan yang dapat dibagikan.

Sebelum memakai clone ini, periksa aturan bootstrap Super Admin di `app/Http/Controllers/Auth/SocialiteController.php`. Saat ini penetapan Super Admin terkait dengan alamat email yang ditulis di controller. Ganti dengan akun Google administrator milik instalasi Anda sebelum login pertama, lalu batasi atau hapus mekanisme bootstrap tersebut setelah akun administrator berhasil disiapkan.

## Halaman utama

| Halaman | URL | Keterangan |
| --- | --- | --- |
| Dashboard | `/dashboard` | Ringkasan akun dan widget referral jika pengguna memiliki kode aktif |
| Pendaftaran PMB | `/pmb` | Form pendaftaran dan informasi pembayaran calon mahasiswa |
| Ujian CBT peserta | `/pmb/exams` | Daftar ujian yang sesuai dengan jalur pendaftaran pengguna |
| Admin PMB | `/admin/pmb` | Ringkasan, data pendaftar, periode, jalur, program studi, biaya, referral |
| Manajemen CBT | `/admin/cbt` | Pembuatan ujian, pengaturan jalur, soal, dan penilaian |
| Manajemen pengguna | `/admin/users/data` | Pengelolaan akun pengguna |
| Manajemen role | `/admin/roles` | Pengelolaan role dan permission |

Halaman admin memerlukan role dan permission yang sesuai. Menu saja tidak memberikan akses; route admin juga memeriksa permission di server.

## Role PMB

Seeder `PmbRoleSeeder` menyiapkan role berikut pada guard `web` dan `api`. Untuk penggunaan aplikasi web, pilih role dengan guard **web**.

| Role | Hak akses utama |
| --- | --- |
| `Admin PMB` | Pengelolaan PMB, pendaftar, pembayaran, dan CBT |
| `Petugas Pendaftaran PMB` | Melihat, mengubah data, dan memproses status pendaftar |
| `Verifikator Berkas PMB` | Melihat pendaftar dan memperbarui status verifikasi |
| `Petugas Keuangan PMB` | Melihat pendaftar dan memverifikasi pembayaran |
| `Pimpinan PMB` | Melihat ringkasan dan data PMB |
| `Pengelola CBT` | Mengelola CBT tanpa hak pengelolaan PMB lainnya |
| `Marketing PMB` | Penanda role marketing; admin perlu mengaitkan akun dengan kode referral aktif |

Role dapat diberikan melalui halaman manajemen role/pengguna oleh administrator. `Super Admin` memiliki akses penuh. Permission `pmb-manage` yang sudah digunakan role kampus lama tetap dapat membuka manajemen CBT untuk menjaga kompatibilitas.

Untuk menyiapkan role PMB saja pada database yang sudah dimigrasikan:

```bash
php artisan db:seed --class=Database\\Seeders\\PmbRoleSeeder
```

## Menyiapkan PMB dan CBT

1. Buat periode pendaftaran aktif dengan tanggal yang mencakup hari ini.
2. Tambahkan jalur pendaftaran dan program studi. ID eksternal DIKTI pada program studi menerima format UUID.
3. Atur biaya pendaftaran dan SPP per kombinasi periode, jalur, dan program studi. Halaman biaya mendukung pengisian untuk semua program studi sekaligus.
4. Pendaftar harus memiliki akun dan pendaftaran pada jalur terkait.
5. Admin CBT membuat ujian, memilih satu atau lebih jalur peserta, mengatur jadwal/durasi, lalu menambahkan soal sebelum menerbitkan ujian.
6. Pilihan ganda dinilai otomatis. Jawaban esai perlu dinilai oleh petugas. Gambar disimpan pada public storage; buat storage link seperti pada langkah instalasi.
7. Untuk referral, admin membuat kode untuk akun pengguna. Pendaftar yang membuka tautan referral aktif lalu mengirim pendaftaran akan tercatat pada referral tersebut.

## API dan Passport

Endpoint API didefinisikan di `routes/api.php` dan umumnya memakai prefiks `/api`. Beberapa endpoint yang tersedia:

| Endpoint | Kegunaan |
| --- | --- |
| `POST /api/v1/login-client` | Login client API |
| `GET /api/user-profile` | Profil pengguna, role, dan permission |
| `POST /api/v1/logout-client` | Logout client |
| `POST /api/v1/sync-role-siakad` | Sinkronisasi role SIAKAD |

Konfigurasikan client Passport dan middleware sesuai kebutuhan integrasi. Jangan masukkan token atau secret API ke README atau repositori.

## Pengujian

Jalankan test aplikasi dengan:

```bash
php artisan test
```

## Rencana integrasi lanjutan

- Payment gateway dan pembaruan status pembayaran lewat webhook terverifikasi.
- Matriks penilaian RPL dan alur asesor yang dapat diaudit.
- Verifikasi identitas dan ekstraksi dokumen dengan persetujuan pengguna.
- Skema komisi referral berdasarkan pembayaran terverifikasi dan kebijakan kampus.
- Provisioning mahasiswa serta sinkronisasi ke Neo Feeder setelah registrasi ulang.
