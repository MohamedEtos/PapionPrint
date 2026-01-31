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
        Schema::table('laser_orders', function (Blueprint $table) {
            $table->integer('required_pieces')->default(0)->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laser_orders', function (Blueprint $table) {
            $table->dropColumn('required_pieces');
        });
    }
};
