<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmb_study_programs', function (Blueprint $table) {
            $table->uuid('id_dikti')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('pmb_study_programs', function (Blueprint $table) {
            $table->dropUnique(['id_dikti']);
            $table->dropColumn('id_dikti');
        });
    }
};
