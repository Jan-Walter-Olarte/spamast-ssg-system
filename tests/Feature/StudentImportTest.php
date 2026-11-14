<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = 'first_name,middle_name,last_name,email,student_no,sex,year_level,program_code';

    // An Officer, plus a program with the code BSIT, so rows have something to point at.
    private function setUpOfficer(): User
    {
        $institute = Institute::create(['code' => 'ICT', 'name' => 'Computing']);
        Program::create(['institute_id' => $institute->id, 'code' => 'BSIT', 'name' => 'Information Technology']);

        // The Student role must exist for the import to find it.
        $this->makeUser('Student');

        return $this->makeUser('Officer');
    }

    private function csv(array $lines): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('students.csv', implode("\n", $lines));
    }

    private function upload(User $officer, array $lines)
    {
        return $this->actingAs($officer)->post(route('officer.students.import.store'), [
            'file' => $this->csv($lines),
        ]);
    }

    public function test_a_valid_file_imports_every_student(): void
    {
        $officer = $this->setUpOfficer();

        $this->upload($officer, [
            self::HEADER,
            'Maria,Cruz,Santos,maria@test.local,2026-1001,Female,2,BSIT',
            'Juan,,Dela Cruz,juan@test.local,2026-1002,male,1,bsit', // capitals do not matter
        ])->assertRedirect(route('officer.students.index'))
          ->assertSessionHas('success');

        $this->assertDatabaseHas('students', ['student_no' => '2026-1001', 'sex' => 'Female', 'year_level' => 2]);
        $this->assertDatabaseHas('students', ['student_no' => '2026-1002', 'sex' => 'Male']);

        $user = User::where('email', 'maria@test.local')->firstOrFail();

        $this->assertTrue($user->hasRole('Student'));
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue(Hash::check('2026-1001', $user->password)); // the student number is the temporary password
    }

    public function test_one_bad_row_imports_nothing_and_reports_the_line(): void
    {
        $officer = $this->setUpOfficer();

        $this->upload($officer, [
            self::HEADER,
            'Maria,,Santos,maria@test.local,2026-1001,Female,2,BSIT',   // line 2: fine
            'Pedro,,Reyes,pedro@test.local,2026-1002,Male,9,ZZZ',       // line 3: bad year and program
        ])->assertRedirect()
          ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseMissing('users', ['email' => 'maria@test.local']);

        $errors = session('import_errors');

        $this->assertSame(3, $errors[0]['line']);
    }

    public function test_an_email_repeated_in_the_file_is_refused(): void
    {
        $officer = $this->setUpOfficer();

        $this->upload($officer, [
            self::HEADER,
            'Maria,,Santos,same@test.local,2026-1001,Female,2,BSIT',
            'Ana,,Reyes,SAME@test.local,2026-1002,Female,2,BSIT',
        ])->assertSessionHas('import_errors');

        $this->assertDatabaseCount('students', 0);
    }

    public function test_an_email_that_already_exists_is_refused(): void
    {
        $officer  = $this->setUpOfficer();
        $existing = $this->makeUser('Student');

        $this->upload($officer, [
            self::HEADER,
            "Maria,,Santos,{$existing->email},2026-1001,Female,2,BSIT",
        ])->assertSessionHas('import_errors');

        $this->assertDatabaseCount('students', 0);
    }

    public function test_a_file_missing_a_column_is_refused(): void
    {
        $officer = $this->setUpOfficer();

        $this->upload($officer, [
            'first_name,last_name,email',
            'Maria,Santos,maria@test.local',
        ])->assertSessionHas('import_fatal');

        $this->assertDatabaseCount('students', 0);
    }

    public function test_the_import_is_written_to_the_audit_log(): void
    {
        $officer = $this->setUpOfficer();

        $this->upload($officer, [
            self::HEADER,
            'Maria,,Santos,maria@test.local,2026-1001,Female,2,BSIT',
        ]);

        $this->assertDatabaseHas('audit_logs', ['action' => 'students.imported']);
    }

    public function test_only_officers_can_open_or_use_the_import(): void
    {
        $this->setUpOfficer();

        foreach (['Student', 'Adviser', 'Admin'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->get(route('officer.students.import'))
                ->assertForbidden();

            $this->actingAs($this->makeUser($role))
                ->post(route('officer.students.import.store'), ['file' => $this->csv([self::HEADER])])
                ->assertForbidden();
        }

        $this->actingAs($this->makeUser('Officer'))
            ->get(route('officer.students.import'))
            ->assertOk()
            ->assertSee('Import Students');
    }
}
