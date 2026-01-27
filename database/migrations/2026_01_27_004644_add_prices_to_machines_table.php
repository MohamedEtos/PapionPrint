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
        Schema::table('machines', function (Blueprint $table) {
            $table->decimal('price_4_pass', 10, 2)->default(0)->after('timePrintPerHour');
            $table->decimal('price_6_pass', 10, 2)->default(0)->after('price_4_pass');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['price_4_pass', 'price_6_pass']);
        });
    }
};
