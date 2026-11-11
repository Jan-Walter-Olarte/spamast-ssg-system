<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // unique: one student record per user. cascadeOnDelete: deleting the user deletes this row.
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            // restrictOnDelete: a program cannot be deleted while it still has students.
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->string('student_no')->unique();
            $table->enum('sex', ['Male', 'Female']);
            $table->unsignedTinyInteger('year_level'); // 1, 2, 3, 4 ...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
