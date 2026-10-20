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
        Schema::create('pmb_study_programs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prodi')->unique(); // Disesuaikan dengan kode Neo Feeder
            $table->string('nama_prodi');
            $table->string('jenjang'); // S1, D3, S2
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_study_programs');
    }
};
