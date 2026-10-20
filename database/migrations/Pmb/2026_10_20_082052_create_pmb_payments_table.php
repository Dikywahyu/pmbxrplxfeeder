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
        Schema::create('pmb_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('pmb_registrations')->cascadeOnDelete();

            $table->enum('jenis_tagihan', ['pendaftaran', 'spp', 'daftar_ulang']);
            $table->decimal('nominal_tagihan', 15, 2);
            $table->string('file_bukti_bayar')->nullable();

            $table->enum('status', ['pending', 'menunggu_verifikasi', 'lunas', 'ditolak'])->default('pending');

            // Mencatat admin siapa yang melakukan verifikasi lunas/tolak
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_payments');
    }
};
