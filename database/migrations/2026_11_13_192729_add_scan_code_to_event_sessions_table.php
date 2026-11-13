<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->string('scan_code', 6)->nullable();
        });

        // Give every session that already exists its own code.
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        foreach (DB::table('event_sessions')->whereNull('scan_code')->pluck('id') as $id) {
            $code = '';

            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            DB::table('event_sessions')->where('id', $id)->update(['scan_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->dropColumn('scan_code');
        });
    }
};
