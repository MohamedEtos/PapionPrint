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
            $table->string('fabrictype')->nullable();
            $table->string('fabricsrc')->nullable();
            $table->string('fabriccode')->nullable();
            $table->string('fabricwidth')->nullable();
            $table->string('meters')->nullable();
            $table->boolean('status')->default(0); 
            $table->boolean('paymentstatus')->default(0);
            $table->string('papyershild')->nullable();
            $table->string('price')->nullable();
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
