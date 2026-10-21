<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_candidate_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->unique()->constrained('pmb_candidates')->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        foreach (['web', 'api'] as $guard) {
            $permission = Permission::firstOrCreate([
                'name' => 'pmb-marketing',
                'guard_name' => $guard,
            ]);
            $marketingRole = Role::where('name', 'Marketing PMB')->where('guard_name', $guard)->first();
            if ($marketingRole) {
                $marketingRole->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        foreach (['web', 'api'] as $guard) {
            $permission = Permission::where('name', 'pmb-marketing')->where('guard_name', $guard)->first();
            if (! $permission) {
                continue;
            }

            $marketingRole = Role::where('name', 'Marketing PMB')->where('guard_name', $guard)->first();
            if ($marketingRole) {
                $marketingRole->revokePermissionTo($permission);
            }
            $permission->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Schema::dropIfExists('pmb_candidate_claims');
    }
};
