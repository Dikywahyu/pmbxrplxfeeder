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
        Schema::create('pmb_registrations', function (Blueprint $table) {
            $table->id();
            // Relasi ke Biodata (Kandidat)
            $table->foreignId('candidate_id')->constrained('pmb_candidates')->cascadeOnDelete();

            // Relasi ke Periode Pendaftaran
            $table->foreignId('period_id')->constrained('pmb_periods');

            // Siapa yang mengetik data ini (Admin/Pembimbing/Mhs)
            $table->foreignId('input_by')->nullable()->constrained('users')->onDelete('set null');

            // Siapa pemilik link referral (Pemberi informasi/Pembawa)
            $table->foreignId('referral_by')->nullable()->constrained('users')->onDelete('set null');

            // Relasi ke Jalur Pendaftaran (Menggantikan admission_path_id)
            $table->foreignId('path_id')->nullable()->constrained('pmb_paths')->onDelete('set null');

            // Pilihan Program Studi (Karena biasanya ada Pilihan 1 & 2)
            // Pastikan tabel feeder_prodi sudah dibuat atau arahkan ke pmb_study_programs
            $table->foreignId('prodi_id_1')->nullable()->constrained('pmb_study_programs')->onDelete('set null');
            $table->foreignId('prodi_id_2')->nullable()->constrained('pmb_study_programs')->onDelete('set null');

            $table->string('nomor_pendaftaran')->unique();
            $table->enum('status', ['draft', 'berkas_masuk', 'review', 'lulus', 'ditolak'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_registrations');
    }
};
