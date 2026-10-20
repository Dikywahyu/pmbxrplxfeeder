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
        Schema::create('pmb_rpl_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('pmb_registrations')->cascadeOnDelete();

            $table->string('kode_mk');
            $table->string('nama_mk');
            $table->integer('sks_diakui');
            $table->string('nilai_huruf', 2); // A, B, C
            $table->text('dasar_penilaian')->nullable(); // Portofolio PKL atau Ijazah Lama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_rpl_conversions');
    }
};
