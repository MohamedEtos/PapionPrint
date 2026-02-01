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
        Schema::create('tarter_prices', function (Blueprint $table) {
            $table->id();
            $table->string('size');
            $table->decimal('price', 10, 4);
            $table->string('type')->default('needle'); // needle, paper, global, machine_time_cost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarter_prices');
    }
};
