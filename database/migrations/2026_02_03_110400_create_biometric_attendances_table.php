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
        Schema::create('biometric_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biometric_user_id')->nullable()->constrained('biometric_users')->onDelete('cascade');
            $table->date('date');
            
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
            
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            
            $table->string('status')->default('absent'); // present, absent, holiday, weekend, leave
            
            $table->integer('delay_minutes')->default(0);
            $table->decimal('delay_deduction', 8, 2)->default(0); // Value to deduct from salary
            
            $table->integer('overtime_minutes')->default(0);
            $table->decimal('overtime_pay', 8, 2)->default(0); // Value to add to salary

             // Absence deduction
            $table->decimal('absence_deduction', 8, 2)->default(0);

            $table->boolean('is_friday')->default(false);
            $table->boolean('is_holiday')->default(false);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();

             // Helper index for faster queries by user/date
            $table->unique(['biometric_user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_attendances');
    }
};
