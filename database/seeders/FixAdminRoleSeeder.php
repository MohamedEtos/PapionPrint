<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class FixAdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Create 'admin' role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Find the first user (usually the main admin/developer in dev env)
        $user = User::first();

        if ($user) {
            $user->assignRole($role);
            $this->command->info("Assigned 'admin' role to user: {$user->email}");
        } else {
            $this->command->error("No users found to assign role.");
        }
    }
}
