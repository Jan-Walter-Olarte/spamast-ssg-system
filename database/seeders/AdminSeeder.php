<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // The Admin role must already exist (RoleSeeder creates it).
        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        // firstOrCreate: look for this email first; create the user only if missing.
        User::firstOrCreate(
            ['email' => 'admin@spamast.edu.ph'],
            [
                'first_name' => 'System',
                'last_name'  => 'Administrator',
                'password'   => Hash::make('Admin@12345'),
                'is_admin'   => true,
                'is_active'  => true,
                'role_id'    => $adminRole->id,
            ]
        );
    }
}
