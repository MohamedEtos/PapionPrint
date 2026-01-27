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
        Schema::table('stras_prices', function (Blueprint $table) {
            $table->string('type')->default('stras')->after('price'); // stras, paper, global
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stras_prices', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
