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
        Schema::create('pmb_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')->constrained('pmb_paths')->cascadeOnDelete();
            $table->string('nama_dokumen'); // cth: Scan KTP, Portofolio PKL
            $table->boolean('is_required')->default(true);
            $table->string('format_file')->default('pdf,jpg,png'); // validasi mime type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_document_requirements');
    }
};
