<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('admin user is seeded with the super admin role and every permission', function (): void {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'useradmin@gmail.com')->firstOrFail();

    expect(Hash::check('password', $admin->password))->toBeTrue()
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and($admin->hasRole('super_admin'))->toBeTrue()
        ->and(Permission::query()->count())->toBeGreaterThan(0)
        ->and($admin->getAllPermissions()->count())->toBe(Permission::query()->count());
});

test('admin user seeder can run more than once without duplicating the user', function (): void {
    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    expect(User::query()->where('email', 'useradmin@gmail.com')->count())->toBe(1);
});

test('seeded admin is authorized for every permission', function (): void {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'useradmin@gmail.com')->firstOrFail();

    Permission::query()->pluck('name')->each(
        fn (string $permission) => expect($admin->can($permission))->toBeTrue()
    );
});
