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
        Schema::create('rollpresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderId')->nullable()->constrained('printers')->cascadeOnDelete();
            $table->foreignId('customerId')->constrained('customers')->cascadeOnDelete();
            $table->string('fabrictype')->nullable();
            $table->string('fabricsrc');
            $table->string('fabriccode')->nullable();
            $table->double('fabricwidth')->nullable();
            $table->double('meters');            
            $table->boolean('status')->default(false);
            $table->boolean('paymentstatus')->default(false);      
            $table->double('papyershild')->nullable();      
            $table->double('price')->nullable();            
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
        Schema::dropIfExists('rollpresses');
    }
};
