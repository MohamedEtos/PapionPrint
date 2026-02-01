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
        Schema::create('stras_prices', function (Blueprint $table) {
            $table->id();
            $table->string('size')->unique();
            $table->decimal('price', 10,4);
            $table->string('type')->default('stras'); // stras, paper, global

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stras_prices');
    }
};
