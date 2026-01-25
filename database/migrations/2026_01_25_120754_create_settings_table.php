<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Papion System');
            $table->string('site_logo')->nullable();
            $table->string('primary_color')->default('#7367F0'); // Vuexy default purple
            $table->string('secondary_color')->default('#EA5455'); // Vuexy default red
            $table->string('success_color')->default('#28C76F')->after('secondary_color');
            $table->string('danger_color')->default('#EA5455')->after('success_color');
            $table->string('warning_color')->default('#FF9F43')->after('danger_color');
            $table->string('info_color')->default('#00CFDD')->after('warning_color');
            $table->string('dark_color')->default('#1E1E1E')->after('info_color');
            $table->timestamps();
        });

        // Insert default row
        DB::table('settings')->insert([
            'site_name' => 'Papion System',
            'primary_color' => '#7367F0',
            'secondary_color' => '#EA5455',
            'success_color' => '#28C76F',
            'danger_color' => '#EA5455',
            'warning_color' => '#FF9F43',
            'info_color' => '#00CFDD',
            'dark_color' => '#1E1E1E',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
