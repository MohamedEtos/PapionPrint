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
            $table->string('site_name')->default('Papion Print');
            $table->string('site_logo')->nullable();
            $table->string('primary_color')->default('#7367F0'); // Vuexy default purple
            $table->string('secondary_color')->default('#EA5455'); // Vuexy default red
            $table->timestamps();
        });

        // Insert default row
        DB::table('settings')->insert([
            'site_name' => 'Papion Print',
            'primary_color' => '#7367F0',
            'secondary_color' => '#EA5455',
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
