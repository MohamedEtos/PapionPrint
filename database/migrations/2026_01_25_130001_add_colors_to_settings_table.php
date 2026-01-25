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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('success_color')->default('#28C76F')->after('secondary_color');
            $table->string('danger_color')->default('#EA5455')->after('success_color');
            $table->string('warning_color')->default('#FF9F43')->after('danger_color');
            $table->string('info_color')->default('#00CFDD')->after('warning_color');
            $table->string('dark_color')->default('#1E1E1E')->after('info_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['success_color', 'danger_color', 'warning_color', 'info_color', 'dark_color']);
        });
    }
};
