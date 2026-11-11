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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('venue');
            $table->date('event_date');
            $table->enum('day_type', ['whole_day', 'half_day']);
            $table->enum('availability', ['scheduled', 'manual'])->default('scheduled');
            $table->decimal('fine_per_miss', 8, 2)->default(0);
            // nullOnDelete: if the creator's account is deleted, the event stays and created_by becomes NULL.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
