<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmb_candidates', function (Blueprint $table) {
            $table->unsignedTinyInteger('desil')->nullable()->after('terdaftar_dtks');
        });
    }

    public function down(): void
    {
        Schema::table('pmb_candidates', function (Blueprint $table) {
            $table->dropColumn('desil');
        });
    }
};
