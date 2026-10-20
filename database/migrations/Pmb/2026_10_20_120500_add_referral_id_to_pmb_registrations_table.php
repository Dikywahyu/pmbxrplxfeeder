<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->foreignId('referral_id')
                ->nullable()
                ->after('referral_by')
                ->constrained('pmb_referrals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referral_id');
        });
    }
};
