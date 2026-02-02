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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Creator
            $table->unsignedBigInteger('customer_id')->nullable(); 
            $table->string('status')->default('draft'); // draft, sent
            $table->decimal('total_amount', 10, 2)->default(0); 
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relation to order items
            $table->unsignedBigInteger('itemable_id');
            $table->string('itemable_type');
            $table->index(['itemable_id', 'itemable_type']);

            $table->decimal('custom_price', 10, 2)->nullable(); // Override calculated price if needed
            $table->integer('quantity')->default(1); 
            $table->text('custom_details')->nullable();
            $table->date('sent_date')->nullable();
            $table->enum('sent_status', ['pending', 'sent', 'delivered'])->default('pending');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
