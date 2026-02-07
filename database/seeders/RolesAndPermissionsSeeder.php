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

        $designer = Permission::create(['name' => 'التصميم', 'guard_name' => 'web']);
        $print = Permission::create(['name' => 'الطباعه', 'guard_name' => 'web']);
        $print = Permission::create(['name' => 'حذف الطباعه', 'guard_name' => 'web']);
        $print = Permission::create(['name' => 'تعديل الطباعه', 'guard_name' => 'web']);
        $stras = Permission::create(['name' => 'الاستراس', 'guard_name' => 'web']);
        $stras = Permission::create(['name' => 'حذف الاستراس', 'guard_name' => 'web']);
        $stras = Permission::create(['name' => 'تعديل الاستراس', 'guard_name' => 'web']);
        $tarter = Permission::create(['name' => 'الترتر', 'guard_name' => 'web']);
        $tarter = Permission::create(['name' => 'حذف الترتر', 'guard_name' => 'web']);
        $tarter = Permission::create(['name' => 'تعديل الترتر', 'guard_name' => 'web']);
        $press = Permission::create(['name' => 'المكبس', 'guard_name' => 'web']);
        $press = Permission::create(['name' => 'حذف المكبس', 'guard_name' => 'web']);
        $press = Permission::create(['name' => 'تعديل المكبس', 'guard_name' => 'web']);
        $laser = Permission::create(['name' => 'الليزر', 'guard_name' => 'web']);
        $laser = Permission::create(['name' => 'حذف الليزر', 'guard_name' => 'web']);
        $laser = Permission::create(['name' => 'تعديل الليزر', 'guard_name' => 'web']);
        $inventory = Permission::create(['name' => 'المخزن', 'guard_name' => 'web']);
        $customers = Permission::create(['name' => 'العملاء', 'guard_name' => 'web']);
        $statistics = Permission::create(['name' => 'الاحصائيات', 'guard_name' => 'web']);
        $invoices = Permission::create(['name' => 'الفواتير', 'guard_name' => 'web']);
        $salaries = Permission::create(['name' => 'الرواتب', 'guard_name' => 'web']);
        $error_reports = Permission::create(['name' => 'تقارير الاخطاء', 'guard_name' => 'web']);


        // create roles and assign created permissions

        // this can be done as separate statements


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
