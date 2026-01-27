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
        Schema::create('stras_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stras_id')->constrained('stras')->onDelete('cascade');
            $table->string('size');
            $table->integer('count');
            $table->decimal('price', 10, 2)->nullable(); // Snapshot of price per piece

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stras_layers');
    }
};
