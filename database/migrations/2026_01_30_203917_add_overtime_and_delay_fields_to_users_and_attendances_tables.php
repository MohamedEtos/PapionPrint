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

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('overtime_rate');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['overtime_hours', 'delay_minutes', 'total_hours']);
        });
    }
};
