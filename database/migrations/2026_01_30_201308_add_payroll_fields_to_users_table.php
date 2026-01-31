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
            $table->decimal('base_salary', 10, 2)->nullable()->after('email');
            $table->integer('working_hours')->default(8)->after('base_salary');
            $table->time('shift_start')->nullable()->after('working_hours');
            $table->time('shift_end')->nullable()->after('shift_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
