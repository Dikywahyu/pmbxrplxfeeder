<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agamas', function (Blueprint $table) {
            $table->id();
            $table->string('id_agama', 5)->unique()->nullable();
            $table->string('nama_agama');
            $table->timestamps();
        });

        Schema::create('alat_transportasis', function (Blueprint $table) {
            $table->id();
            $table->string('id_alat_transportasi', 25)->unique()->nullable();
            $table->string('nama_alat_transportasi');
            $table->timestamps();
        });

        Schema::create('penghasilans', function (Blueprint $table) {
            $table->id();
            $table->string('id_penghasilan', 20)->unique()->nullable();
            $table->string('nama_penghasilan', 100);
            $table->timestamps();
        });

        Schema::create('wilayahs', function (Blueprint $table) {
            $table->id();
            $table->string('id_wilayah', 10)->unique();
            $table->string('nama_wilayah', 150);
            $table->string('id_level_wilayah', 5)->nullable();
            $table->string('id_negara', 5)->nullable();
            $table->string('id_induk_wilayah', 10)->nullable();
            $table->timestamps();
            $table->index(['id_level_wilayah', 'id_induk_wilayah']);
            $table->index(['id_negara', 'id_level_wilayah']);
        });

        Schema::table('pmb_candidates', function (Blueprint $table) {
            $table->string('id_alat_transportasi', 25)->nullable()->after('id_agama');
        });
    }

    public function down(): void
    {
        Schema::table('pmb_candidates', function (Blueprint $table) {
            $table->dropColumn('id_alat_transportasi');
        });

        Schema::dropIfExists('wilayahs');
        Schema::dropIfExists('penghasilans');
        Schema::dropIfExists('alat_transportasis');
        Schema::dropIfExists('agamas');
    }
};
