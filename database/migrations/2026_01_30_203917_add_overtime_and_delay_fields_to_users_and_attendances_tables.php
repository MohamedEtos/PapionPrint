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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('overtime_rate', 4, 2)->default(1.5)->after('base_salary');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('overtime_hours', 5, 2)->default(0)->after('status');
            $table->integer('delay_minutes')->default(0)->after('overtime_hours');
            $table->decimal('total_hours', 5, 2)->default(0)->after('delay_minutes');
        });
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
