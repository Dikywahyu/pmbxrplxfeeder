<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->enum('status', [
                'draft', 'berkas_masuk', 'review', 'sudah_ujian', 'lulus', 'diterima', 'ditolak', 'mundur',
            ])->default('draft')->change();
        });
    }

    public function down(): void
    {
        DB::table('pmb_registrations')->where('status', 'sudah_ujian')->update(['status' => 'review']);
        DB::table('pmb_registrations')->where('status', 'diterima')->update(['status' => 'lulus']);
        DB::table('pmb_registrations')->where('status', 'mundur')->update(['status' => 'berkas_masuk']);

        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->enum('status', ['draft', 'berkas_masuk', 'review', 'lulus', 'ditolak'])
                ->default('draft')->change();
        });
    }
};
