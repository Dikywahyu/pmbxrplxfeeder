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
        Schema::create('pmb_candidate_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('pmb_registrations')->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained('pmb_document_requirements')->cascadeOnDelete();

            $table->string('file_path');
            $table->enum('status_verifikasi', ['pending', 'valid', 'invalid'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_candidate_documents');
    }
};
