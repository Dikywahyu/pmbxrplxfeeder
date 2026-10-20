<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pmb_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('pmb_exams')->cascadeOnDelete();
            $table->string('type', 20)->default('multiple_choice');
            $table->longText('question_text');
            $table->string('question_image_path')->nullable();
            $table->json('options')->nullable();
            $table->string('correct_option', 10)->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['exam_id', 'position']);
        });

        Schema::create('pmb_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('pmb_exams')->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('pmb_registrations')->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->string('status', 20)->default('in_progress');
            $table->decimal('score', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['exam_id', 'registration_id']);
        });

        Schema::create('pmb_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('pmb_exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('pmb_exam_questions')->cascadeOnDelete();
            $table->text('selected_option')->nullable();
            $table->longText('answer_text')->nullable();
            $table->string('answer_image_path')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('grader_note')->nullable();
            $table->timestamps();
            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_exam_answers');
        Schema::dropIfExists('pmb_exam_attempts');
        Schema::dropIfExists('pmb_exam_questions');
        Schema::dropIfExists('pmb_exams');
    }
};
