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
        Schema::create('biometric_users', function (Blueprint $table) {
            $table->id();
            $table->integer('biometric_id')->unique(); // ID from the device
            $table->string('name')->nullable();
            
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
            
            $table->decimal('base_salary', 10, 2)->default(0); 
            // Or use an hourly rate calculation? User asked for "Salary" generally.
            
            $table->decimal('overtime_rate', 8, 2)->default(1.5); // Multiplier or fixed value? Usually multiplier.
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_users');
    }
};
