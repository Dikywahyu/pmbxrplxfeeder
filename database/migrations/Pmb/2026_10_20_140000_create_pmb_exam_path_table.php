<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_exam_path', function (Blueprint $table) {
            $table->foreignId('exam_id')->constrained('pmb_exams')->cascadeOnDelete();
            $table->foreignId('path_id')->constrained('pmb_paths')->cascadeOnDelete();
            $table->primary(['exam_id', 'path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_exam_path');
    }
};
