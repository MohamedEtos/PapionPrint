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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['paper', 'ink']);
            $table->string('machine_type'); // 'dtf' or 'sublimation'
            $table->string('color')->nullable(); // For ink
            $table->decimal('quantity', 8, 2)->default(0);
            $table->string('unit')->default('unit'); // liter, meter
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
