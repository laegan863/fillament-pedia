<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the administrator account with the super admin role and every permission.
     */
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'administrator',
            '--option' => 'permissions',
            '--no-interaction' => true,
        ]);

        $superAdminRole = Role::findOrCreate(config('filament-shield.super_admin.name'), 'web');
        $superAdminRole->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        $admin = User::query()->updateOrCreate(
            ['email' => 'useradmin@gmail.com'],
            [
                'name' => 'User Admin',
                'password' => 'password',
            ],
        );

        $admin->forceFill(['email_verified_at' => $admin->email_verified_at ?? now()])->save();
        $admin->assignRole($superAdminRole);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
