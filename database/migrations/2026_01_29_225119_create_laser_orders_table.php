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
        Schema::create('laser_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable(); 
            $table->enum('source', ['client', 'ap_group'])->default('ap_group');
            $table->boolean('add_ceylon')->default(false);
            $table->double('height')->nullable(); // cm
            $table->double('width')->nullable(); // cm
            $table->integer('pieces_per_section')->default(1);
            $table->integer('section_count')->default(1);
            $table->text('notes')->nullable();
            $table->decimal('manufacturing_cost', 10, 2)->default(0); // Cost per piece
            $table->decimal('total_cost', 10, 2)->default(0); // Total cost for order
            $table->string('image_path')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('laser_materials')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laser_orders');
    }
};
