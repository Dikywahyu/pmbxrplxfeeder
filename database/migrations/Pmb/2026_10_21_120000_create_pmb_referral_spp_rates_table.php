<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_referral_spp_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('pmb_referrals')->cascadeOnDelete();
            $table->foreignId('fee_id')->constrained('pmb_fees')->cascadeOnDelete();
            $table->decimal('nominal_spp', 15, 2);
            $table->timestamps();
            $table->unique(['referral_id', 'fee_id']);
        });

        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->decimal('nominal_spp', 15, 2)->nullable()->after('prodi_id_1');
        });

        DB::table('pmb_registrations')
            ->join('pmb_fees', function ($join) {
                $join->on('pmb_fees.period_id', '=', 'pmb_registrations.period_id')
                    ->on('pmb_fees.path_id', '=', 'pmb_registrations.path_id')
                    ->on('pmb_fees.program_id', '=', 'pmb_registrations.prodi_id_1');
            })
            ->update(['pmb_registrations.nominal_spp' => DB::raw('pmb_fees.nominal_spp')]);
    }

    public function down(): void
    {
        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->dropColumn('nominal_spp');
        });

        Schema::dropIfExists('pmb_referral_spp_rates');
    }
};
