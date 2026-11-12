<?php

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Every sample account uses this password (hashed once, reused below).
        $password = Hash::make('Student@12345');

        // The roles must already exist (RoleSeeder creates them).
        $officerRole = Role::where('name', 'Officer')->firstOrFail();
        $adviserRole = Role::where('name', 'Adviser')->firstOrFail();
        $studentRole = Role::where('name', 'Student')->firstOrFail();

        // ---------- 1. Institutes and their programs ----------
        $structure = [
            [
                'code' => 'IAG', 'name' => 'Institute of Agriculture',
                'programs' => [
                    ['BSA',   'Bachelor of Science in Agriculture'],
                    ['BSABM', 'Bachelor of Science in Agribusiness Management'],
                ],
            ],
            [
                'code' => 'IFA', 'name' => 'Institute of Fisheries and Aquatic Sciences',
                'programs' => [
                    ['BSFI', 'Bachelor of Science in Fisheries'],
                    ['BSMB', 'Bachelor of Science in Marine Biology'],
                ],
            ],
            [
                'code' => 'ICT', 'name' => 'Institute of Computing and Technology',
                'programs' => [
                    ['BSIT', 'Bachelor of Science in Information Technology'],
                    ['BSCS', 'Bachelor of Science in Computer Science'],
                ],
            ],
            [
                'code' => 'IED', 'name' => 'Institute of Education',
                'programs' => [
                    ['BEED', 'Bachelor of Elementary Education'],
                    ['BSED', 'Bachelor of Secondary Education'],
                ],
            ],
        ];

        $programIds = [];

        foreach ($structure as $item) {
            // updateOrCreate: find by code, update the name, or create it.
            $institute = Institute::updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );

            foreach ($item['programs'] as [$code, $name]) {
                $program = Program::updateOrCreate(
                    ['code' => $code],
                    ['institute_id' => $institute->id, 'name' => $name]
                );

                $programIds[] = $program->id;
            }
        }

        // ---------- 2. One Officer and one Adviser ----------
        User::firstOrCreate(
            ['email' => 'officer@spamast.edu.ph'],
            [
                'first_name' => 'Olivia',
                'last_name'  => 'Ramirez',
                'password'   => $password,
                'role_id'    => $officerRole->id,
                'is_active'  => true,
                'is_admin'   => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'adviser@spamast.edu.ph'],
            [
                'first_name' => 'Adrian',
                'last_name'  => 'Velasco',
                'password'   => $password,
                'role_id'    => $adviserRole->id,
                'is_active'  => true,
                'is_admin'   => false,
            ]
        );

        // ---------- 3. Twenty students (10 female, 10 male) ----------
        $students = [
            ['Maria',     'Santos',     'Female'],
            ['Juan',      'Dela Cruz',  'Male'],
            ['Ana',       'Reyes',      'Female'],
            ['Mark',      'Villanueva', 'Male'],
            ['Katrina',   'Cruz',       'Female'],
            ['Carlo',     'Domingo',    'Male'],
            ['Jasmine',   'Garcia',     'Female'],
            ['Joshua',    'Castillo',   'Male'],
            ['Angelica',  'Mendoza',    'Female'],
            ['Miguel',    'Navarro',    'Male'],
            ['Patricia',  'Flores',     'Female'],
            ['Paolo',     'Aguilar',    'Male'],
            ['Nicole',    'Ramos',      'Female'],
            ['Kevin',     'Salazar',    'Male'],
            ['Bianca',    'Torres',     'Female'],
            ['Rafael',    'Pascual',    'Male'],
            ['Cristina',  'Aquino',     'Female'],
            ['Nathan',    'Lopez',      'Male'],
            ['Daniela',   'Bautista',   'Female'],
            ['Daniel',    'Ocampo',     'Male'],
        ];

        foreach ($students as $index => [$first, $last, $sex]) {
            $number = $index + 1;

            $user = User::firstOrCreate(
                ['email' => sprintf('student%02d@spamast.edu.ph', $number)],
                [
                    'first_name' => $first,
                    'last_name'  => $last,
                    'password'   => $password,
                    'role_id'    => $studentRole->id,
                    'is_active'  => true,
                    'is_admin'   => false,
                ]
            );

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    // Spread students across all programs and year levels 1 to 4.
                    'program_id' => $programIds[$index % count($programIds)],
                    'student_no' => sprintf('2026-%04d', $number),
                    'sex'        => $sex,
                    'year_level' => ($index % 4) + 1,
                ]
            );
        }
    }
}
