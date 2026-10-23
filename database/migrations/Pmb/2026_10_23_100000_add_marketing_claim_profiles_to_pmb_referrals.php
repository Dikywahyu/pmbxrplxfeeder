<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmb_referrals', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('pmb_referrals', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->change();
            $table->string('marketing_name')->nullable()->after('owner_id');
            $table->string('whatsapp', 30)->nullable()->after('marketing_name');
            $table->string('contact_email')->nullable()->after('whatsapp');
            $table->string('claim_token_hash', 64)->nullable()->unique()->after('contact_email');
            $table->timestamp('claim_expires_at')->nullable()->after('claim_token_hash');
            $table->timestamp('claimed_at')->nullable()->after('claim_expires_at');
            $table->foreignId('created_by')->nullable()->after('claimed_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('pmb_referrals', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Keep owner nullable if unclaimed invitations still exist.
        Schema::table('pmb_referrals', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropUnique(['claim_token_hash']);
            $table->dropColumn(['marketing_name', 'whatsapp', 'contact_email', 'claim_token_hash', 'claim_expires_at', 'claimed_at', 'created_by']);
        });
    }
};
