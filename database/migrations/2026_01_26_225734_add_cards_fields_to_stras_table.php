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
        Schema::table('stras', function (Blueprint $table) {
            $table->integer('cards_count')->nullable()->after('width');
            $table->integer('pieces_per_card')->nullable()->after('cards_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stras', function (Blueprint $table) {
            $table->dropColumn('cards_count');
            $table->dropColumn('pieces_per_card');
        });
    }
};
