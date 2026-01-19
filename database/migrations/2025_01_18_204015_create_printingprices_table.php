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
        Schema::create('printingprices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machineId')->constrained('machines')->cascadeOnDelete();
            $table->foreignId('pricePerMeterId')->constrained('printers')->cascadeOnDelete();
            $table->foreignId('totalPriceId')->constrained('printers')->cascadeOnDelete();
            $table->decimal('pricePerMeter', 10, 2);
            $table->decimal('totalPrice', 12, 2);
            $table->decimal('discount', 12, 2);
            $table->decimal('finalPrice', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printingprices');
    }
};
