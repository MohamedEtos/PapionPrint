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
        Schema::create('invoice_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Using item_id logic
            $table->string('order_type'); // Model class name
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->date('sent_date')->nullable();
            $table->enum('sent_status', ['pending', 'sent', 'delivered'])->default('pending');
            $table->string('customer_name')->nullable(); // Snapshot
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['order_id', 'order_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_archives');
    }
};
