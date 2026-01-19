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
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('orderNumber');
            $table->foreignId('customerId')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('machineId')->constrained('machines')->cascadeOnDelete();
            $table->decimal('fileHeight', 10, 2);
            $table->decimal('fileWidth', 10, 2);
            $table->integer('fileCopies');
            $table->integer('picInCopies');
            $table->integer('pass');
            $table->decimal('meters', 10, 2);
            $table->string('status');
            $table->string('paymentStatus')->default('unpaid');
            $table->foreignId('designerId')->constrained('users')->cascadeOnDelete();
            $table->foreignId('operatorId')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('archive')->default(false);
            $table->timestamp('timeEndOpration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
