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
        Schema::create('laser_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0); // Price per meter (or unit)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laser_materials');
    }
};
