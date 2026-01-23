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
            $table->string('fabrictype');
            $table->string('fabricsrc');
            $table->string('fabriccode');
            $table->double('fabricwidth');
            $table->double('meters');            
            $table->boolean('status')->default(false);
            $table->boolean('paymentstatus')->default(false);      
            $table->double('papyershild');      
            $table->double('price');            
            $table->text('notes');
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
