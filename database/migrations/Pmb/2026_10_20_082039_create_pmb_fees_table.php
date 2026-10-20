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
        Schema::create('pmb_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('pmb_periods')->cascadeOnDelete();
            $table->foreignId('path_id')->constrained('pmb_paths')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('pmb_study_programs')->cascadeOnDelete();

            $table->decimal('nominal_pendaftaran', 15, 2)->default(0);
            $table->decimal('nominal_spp', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_fees');
    }
};
