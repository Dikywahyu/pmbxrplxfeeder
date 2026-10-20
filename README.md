<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Penerimaan Mahasiswa Baru (PMB)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for code blocks to look like markdown */
        pre {
            background-color: #1f2937;
            color: #e5e7eb;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased line-height-relaxed p-6 md:p-12">

    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-lg border border-gray-100">
        
        <!-- Header -->
        <div class="text-center border-b pb-8 mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900 mb-4 flex items-center justify-center gap-3">
                <span>🎓</span> Sistem Informasi Penerimaan Mahasiswa Baru (PMB) Terintegrasi
            </h1>
            
            <div class="flex flex-wrap justify-center gap-2 mt-4">
                <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded shadow-sm">Laravel</span>
                <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded shadow-sm">MySQL</span>
                <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded shadow-sm">Spatie Permission</span>
                <span class="px-3 py-1 bg-indigo-500 text-white text-xs font-bold rounded shadow-sm">PDDikti Feeder Ready</span>
            </div>
        </div>

        <!-- Description -->
        <p class="text-gray-600 mb-8 text-lg leading-relaxed text-center">
            Aplikasi Penerimaan Mahasiswa Baru (PMB) modern dan dinamis yang dirancang untuk mengakomodasi berbagai skenario pendaftaran kampus. Sistem ini mendukung jalur pendaftaran Reguler, KIP-Kuliah, hingga RPL (Rekognisi Pembelajaran Lampau) dengan arsitektur <em>database</em> yang sepenuhnya kompatibel dengan standar Web Service PDDikti Neo Feeder.
        </p>

        <!-- Features -->
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-4 mt-8 flex items-center gap-2">
            <span>✨</span> Fitur Utama
        </h2>
        <ul class="space-y-4 mb-8 text-gray-700">
            <li class="flex gap-3"><span class="text-xl">🚀</span> <div><strong>Multi-Jalur Pendaftaran:</strong> Mendukung alur syarat dokumen dan isian formulir yang berbeda untuk pendaftar Reguler, beasiswa KIP-Kuliah, dan jalur RPL.</div></li>
            <li class="flex gap-3"><span class="text-xl">📸</span> <div><strong>Smart OCR KTP:</strong> Pendaftar tidak perlu mengetik ulang data. Sistem otomatis mengekstrak NIK, Nama, Tempat, dan Tanggal Lahir dari hasil <em>scan</em> foto KTP.</div></li>
            <li class="flex gap-3"><span class="text-xl">🔄</span> <div><strong>Modul Konversi RPL & PKL:</strong> <em>Dashboard</em> khusus bagi Asesor untuk melakukan ekuivalensi portofolio pengalaman kerja atau transkrip lama menjadi SKS dan nilai Mata Kuliah.</div></li>
            <li class="flex gap-3"><span class="text-xl">🤝</span> <div><strong>Sistem Referral Agent:</strong> Modul <em>tracking</em> afiliasi menggunakan kode <em>referral</em> bagi mahasiswa lama, dosen, atau agen yang membawa calon mahasiswa baru.</div></li>
            <li class="flex gap-3"><span class="text-xl">💳</span> <div><strong>Manajemen Biaya & SPP Dinamis:</strong> Penetapan tagihan formulir dan SPP secara otomatis berdasarkan kombinasi Gelombang, Jalur Pendaftaran, dan Program Studi, dilengkapi modul unggah bukti bayar manual.</div></li>
            <li class="flex gap-3"><span class="text-xl">🏛️</span> <div><strong>PDDikti Feeder Ready:</strong> Skema <em>database</em> kandidat dan riwayat pendaftaran dipisah (<em>normalized</em>) agar <em>mapping</em> ke <em>endpoint</em> <code>InsertBiodataMahasiswa</code> dan <code>InsertRiwayatPendidikan</code> sangat mudah.</div></li>
            <li class="flex gap-3"><span class="text-xl">🔐</span> <div><strong>Granular Role Access:</strong> Manajemen hak akses fleksibel (Admin, Keuangan, Asesor RPL, dan Kandidat) menggunakan <code>spatie/laravel-permission</code>.</div></li>
        </ul>

        <!-- Tech Stack -->
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-4 mt-8 flex items-center gap-2">
            <span>🛠️</span> Tech Stack
        </h2>
        <ul class="list-disc pl-6 space-y-2 mb-8 text-gray-700">
            <li><strong>Framework:</strong> Laravel (PHP)</li>
            <li><strong>Database:</strong> MySQL / PostgreSQL</li>
            <li><strong>Authentication & Access:</strong> Laravel Auth + Spatie Role & Permission</li>
            <li><strong>Asset & UI:</strong> Tailwind CSS</li>
        </ul>

        <!-- Requirements -->
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-4 mt-8 flex items-center gap-2">
            <span>📋</span> Persyaratan Sistem
        </h2>
        <ul class="list-disc pl-6 space-y-2 mb-8 text-gray-700">
            <li>PHP >= 8.1</li>
            <li>Composer</li>
            <li>MySQL 5.7+ atau MariaDB 10.3+</li>
            <li>Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD/Imagick (untuk unggahan dokumen)</li>
        </ul>

        <!-- Installation -->
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-4 mt-8 flex items-center gap-2">
            <span>⚙️</span> Panduan Instalasi
        </h2>
        <div class="space-y-6 mb-8">
            <div>
                <p class="font-semibold text-gray-800 mb-2">1. Clone repositori ini:</p>
<pre><code>git clone https://github.com/username-anda/repo-pmb.git
cd repo-pmb</code></pre>
            </div>
            
            <div>
                <p class="font-semibold text-gray-800 mb-2">2. Install dependensi Composer:</p>
<pre><code>composer install</code></pre>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-2">3. Konfigurasi Environment:</p>
                <p class="text-gray-600 text-sm mb-2">Salin file `.env.example` menjadi `.env` dan atur koneksi <em>database</em> Anda.</p>
<pre><code>cp .env.example .env
php artisan key:generate</code></pre>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-2">4. Jalankan Migrasi dan Seeder:</p>
                <p class="text-gray-600 text-sm mb-2">Sistem akan meng-inisialisasi struktur tabel, peran Spatie, dan akun Admin <em>default</em>.</p>
<pre><code>php artisan migrate
php artisan db:seed --class=RoleAndPermissionSeeder</code></pre>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-2">5. Tautkan Storage:</p>
                <p class="text-gray-600 text-sm mb-2">Untuk memastikan dokumen KTP, bukti bayar, dan portofolio RPL dapat diakses.</p>
<pre><code>php artisan storage:link</code></pre>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-2">6. Jalankan Server Lokal:</p>
<pre><code>php artisan serve</code></pre>
                <p class="text-gray-600 text-sm mt-2">Aplikasi dapat diakses di <a href="http://localhost:8000" class="text-blue-600 hover:underline">http://localhost:8000</a>.</p>
            </div>
        </div>

        <!-- Database Structure -->
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-4 mt-8 flex items-center gap-2">
            <span>🗄️</span> Struktur Modul Database Utama
        </h2>
        <p class="text-gray-700 mb-4">Aplikasi ini dibagi menjadi dua area fungsionalitas besar:</p>
        <ol class="list-decimal pl-6 space-y-2 mb-8 text-gray-700">
            <li><strong>Master Data (Admin):</strong> <code>pmb_periods</code>, <code>pmb_paths</code>, <code>pmb_study_programs</code>, <code>pmb_fees</code>, <code>pmb_document_requirements</code>.</li>
            <li><strong>Transaksi (Pendaftar):</strong> <code>pmb_candidates</code> (Biodata statis & KIP), <code>pmb_registrations</code> (Histori pilihan prodi), <code>pmb_candidate_documents</code>, <code>pmb_rpl_conversions</code>, <code>pmb_payments</code>, <code>pmb_referrals</code>.</li>
        </ol>

        <hr class="my-8 border-gray-200">
        
        <p class="text-center text-gray-500 italic text-sm">
            Didesain dan dikembangkan khusus untuk kemudahan administrasi akademik perguruan tinggi.
        </p>

    </div>
</body>
</html>