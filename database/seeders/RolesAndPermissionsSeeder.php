<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $editOrders = Permission::create(['name' => 'edit orders', 'guard_name' => 'web']);
        $deleteOrders = Permission::create(['name' => 'delete orders', 'guard_name' => 'web']);
        $publishOrders = Permission::create(['name' => 'publish orders', 'guard_name' => 'web']);
        $unpublishOrders = Permission::create(['name' => 'unpublish orders', 'guard_name' => 'web']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'writer', 'guard_name' => 'web']);
        $role->givePermissionTo($editOrders);

        // or may be done by chaining
        $role = Role::create(['name' => 'moderator', 'guard_name' => 'web']);
        $role->givePermissionTo([$publishOrders, $unpublishOrders]);

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::all());
        
        // Create demo admin user
        $user = User::firstOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]);
        
        $user->assignRole('super-admin');
    }
}
