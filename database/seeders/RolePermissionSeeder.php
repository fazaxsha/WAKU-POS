<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat semua permissions
        $permissions = [
            'pos.access',
            'pos.refund',
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'purchase.create',
            'purchase.confirm',
            'report.view',
            'report.export',
            'user.manage',
            'setting.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Role: Kasir — hanya transaksi POS
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $kasir->syncPermissions(['pos.access', 'product.view']);

        // Role: Admin — operasional harian
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'pos.access',
            'pos.refund',
            'product.view',
            'product.create',
            'product.edit',
            'purchase.create',
            'purchase.confirm',
            'report.view',
        ]);

        // Role: Owner — akses penuh
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        $this->command->info('Roles & permissions berhasil dibuat.');
    }
}