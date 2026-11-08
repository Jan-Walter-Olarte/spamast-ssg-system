<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // The four roles of the system.
        $roles = ['Admin', 'Student', 'Officer', 'Adviser'];

        foreach ($roles as $name) {
            // firstOrCreate: add the role only if it does not exist yet,
            // so running this seeder twice never makes duplicates.
            Role::firstOrCreate(['name' => $name]);
        }
    }
}
