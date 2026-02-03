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
        Schema::table('biometric_attendances', function (Blueprint $table) {
            $table->string('missing_punch')->nullable()->after('status')->comment('check_in or check_out if auto-filled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_attendances', function (Blueprint $table) {
            $table->dropColumn('missing_punch');
        });
    }
};
