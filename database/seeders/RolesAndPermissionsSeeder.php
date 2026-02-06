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

        $print = Permission::create(['name' => 'الطباعه', 'guard_name' => 'web']);
        $stras = Permission::create(['name' => 'الاستراس', 'guard_name' => 'web']);
        $press = Permission::create(['name' => 'المكبس', 'guard_name' => 'web']);
        $laser = Permission::create(['name' => 'الليزر', 'guard_name' => 'web']);
        $inventory = Permission::create(['name' => 'المخزن', 'guard_name' => 'web']);
        $invoices = Permission::create(['name' => 'الفواتير', 'guard_name' => 'web']);
        $salaries = Permission::create(['name' => 'الرواتب', 'guard_name' => 'web']);
        $customers = Permission::create(['name' => 'العملاء', 'guard_name' => 'web']);


        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'printer', 'guard_name' => 'web']);
        $role->givePermissionTo($print,$inventory);

        // or may be done by chaining
        $role = Role::create(['name' => 'strasAndTarter', 'guard_name' => 'web']);
        $role->givePermissionTo([$strasAndTarter]);

        $role = Role::create(['name' => 'press', 'guard_name' => 'web']);
        $role->givePermissionTo([$press]);

        $role = Role::create(['name' => 'laser', 'guard_name' => 'web']);
        $role->givePermissionTo([$laser]);

        $role = Role::create(['name' => 'salaries', 'guard_name' => 'web']);
        $role->givePermissionTo([$salaries]);

        $role = Role::create(['name' => 'customers', 'guard_name' => 'web']);
        $role->givePermissionTo([$customers]);

        $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $role->givePermissionTo([$print,$strasAndTarter,$press,$laser,$inventory,$invoices,$salaries,$customers]);

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
