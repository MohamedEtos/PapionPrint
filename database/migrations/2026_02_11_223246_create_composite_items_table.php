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
        Schema::create('composite_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('laser_cost', 10, 2)->default(0);
            $table->decimal('tarter_cost', 10, 2)->default(0);
            $table->decimal('print_cost', 10, 2)->default(0);
            $table->decimal('stras_cost', 10, 2)->default(0);
            $table->decimal('other_cost', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composite_items');
    }
};
