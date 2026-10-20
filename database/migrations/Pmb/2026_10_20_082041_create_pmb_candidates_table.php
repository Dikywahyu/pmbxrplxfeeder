<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pmb_candidates', function (Blueprint $table) {
            $table->id(); // Akun login mahasiswa 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Identitas Utama
            $table->string('nama_mahasiswa');
            $table->string('nim', 50)->nullable(); // Akan terisi jika sudah lulus & di-generate SIAKAD
            $table->string('id_mahasiswa')->nullable(); // ID dari PDDIKTI (UUID)
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('npwp', 25)->nullable();

            // Kelahiran & Agama
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('id_agama', 5)->nullable();

            // Alamat & Domisili
            $table->string('jalan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('id_wilayah', 10)->nullable(); // Kode Kecamatan dari Feeder
            $table->string('id_negara', 5)->nullable();

            // Kontak & Tempat Tinggal 
            $table->string('telepon', 20)->nullable();
            $table->string('handphone', 20)->nullable();
            $table->string('email')->nullable();

            // ==========================================
            // DATA KESEJAHTERAAN SOSIAL / KIP (BARU)
            // ==========================================
            $table->string('nomor_daftar_kip', 90)->nullable();
            $table->string('nomor_kip', 50)->nullable(); // Nomor Kartu Indonesia Pintar
            $table->string('nomor_kks', 50)->nullable(); // Nomor Kartu Keluarga Sejahtera
            $table->string('nomor_kps', 50)->nullable(); // Nomor Kartu Perlindungan Sosial / PKH
            $table->boolean('penerima_kps')->default(false); // Flag status penerima KPS
            $table->boolean('terdaftar_dtks')->default(false); // Flag status DTKS Kemensos

            // Data Ayah
            $table->string('nik_ayah', 20)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('id_pendidikan_ayah', 5)->nullable();
            $table->string('nama_pendidikan_ayah')->nullable();
            $table->string('id_pekerjaan_ayah', 5)->nullable();
            $table->string('nama_pekerjaan_ayah')->nullable();
            $table->string('id_penghasilan_ayah', 5)->nullable();
            $table->string('nama_penghasilan_ayah')->nullable();

            // Data Ibu
            $table->string('nik_ibu', 20)->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('id_pendidikan_ibu', 5)->nullable();
            $table->string('nama_pendidikan_ibu')->nullable();
            $table->string('id_pekerjaan_ibu', 5)->nullable();
            $table->string('nama_pekerjaan_ibu')->nullable();
            $table->string('id_penghasilan_ibu', 5)->nullable();
            $table->string('nama_penghasilan_ibu')->nullable();

            // Data Wali
            $table->string('nama_wali')->nullable();
            $table->date('tanggal_lahir_wali')->nullable();
            $table->string('id_pendidikan_wali', 5)->nullable();
            $table->string('nama_pendidikan_wali')->nullable();
            $table->string('id_pekerjaan_wali', 5)->nullable();
            $table->string('nama_pekerjaan_wali')->nullable();
            $table->string('id_penghasilan_wali', 5)->nullable();
            $table->string('nama_penghasilan_wali')->nullable();

            // Metadata Feeder & Verifikasi KTP
            $table->dateTime('last_update_feeder')->nullable();
            $table->dateTime('tgl_create_feeder')->nullable();
            $table->string('file_ktp_path')->nullable();
            $table->boolean('is_ktp_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_candidates');
    }
};
