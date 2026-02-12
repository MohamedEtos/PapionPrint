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
        Schema::create('stras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orderId')->nullable();
            $table->unsignedBigInteger('customerId')->nullable();
            $table->double('height')->nullable(); // 
            $table->double('width')->nullable();
            $table->integer('cards_count')->nullable();
            $table->integer('pieces_per_card')->nullable();
            $table->integer('manufacturing_cost')->nullable();
            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stras');
    }
};
