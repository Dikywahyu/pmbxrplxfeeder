<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PmbRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'pmb-access', 'pmb-manage', 'pmb-review', 'pmb-payment-review', 'pmb-cbt-manage', 'pmb-registration-manage', 'pmb-marketing',
        ];

        foreach (['web', 'api'] as $guard) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
            }
        }

        $roles = [
            'Admin PMB' => ['pmb-access', 'pmb-manage', 'pmb-review', 'pmb-payment-review', 'pmb-cbt-manage', 'pmb-registration-manage'],
            'Petugas Pendaftaran PMB' => ['pmb-access', 'pmb-registration-manage', 'pmb-review'],
            'Verifikator Berkas PMB' => ['pmb-access', 'pmb-review'],
            'Petugas Keuangan PMB' => ['pmb-access', 'pmb-payment-review'],
            'Pimpinan PMB' => ['pmb-access'],
            'Pengelola CBT' => ['pmb-cbt-manage'],
            'Marketing PMB' => ['pmb-marketing'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            foreach (['web', 'api'] as $guard) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
                $role->syncPermissions(Permission::where('guard_name', $guard)
                    ->whereIn('name', $rolePermissions)->get());
            }
        }
    }
}
