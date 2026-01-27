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
        Schema::create('tarter_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarter_id')->constrained()->onDelete('cascade');
            $table->string('size'); // Needle Size (mqas el ebra)
            $table->integer('count');
            $table->decimal('price', 10, 4)->nullable(); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarter_layers');
    }
};
