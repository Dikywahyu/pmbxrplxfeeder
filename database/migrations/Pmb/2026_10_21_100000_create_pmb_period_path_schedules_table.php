<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_period_path_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('pmb_periods')->cascadeOnDelete();
            $table->foreignId('path_id')->constrained('pmb_paths')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['period_id', 'path_id']);
        });

        $pairs = DB::table('pmb_fees')->select('period_id', 'path_id')->distinct()->get();
        foreach ($pairs as $pair) {
            $period = DB::table('pmb_periods')->where('id', $pair->period_id)->first();
            if (! $period) {
                continue;
            }

            DB::table('pmb_period_path_schedules')->insert([
                'period_id' => $pair->period_id,
                'path_id' => $pair->path_id,
                'tanggal_mulai' => $period->tanggal_mulai,
                'tanggal_selesai' => $period->tanggal_selesai,
                'is_active' => $period->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_period_path_schedules');
    }
};
