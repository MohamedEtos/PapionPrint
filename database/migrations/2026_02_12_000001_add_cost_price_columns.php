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
        if (!Schema::hasColumn('machines', 'cost_price')) {
            Schema::table('machines', function (Blueprint $table) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('price_6_pass');
            });
        }

        if (!Schema::hasColumn('stras_prices', 'cost_price')) {
            Schema::table('stras_prices', function (Blueprint $table) {
                $table->decimal('cost_price', 10, 4)->default(0)->after('price');
            });
        }

        if (!Schema::hasColumn('tarter_prices', 'cost_price')) {
            Schema::table('tarter_prices', function (Blueprint $table) {
                $table->decimal('cost_price', 10, 4)->default(0)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('machines', 'cost_price')) {
            Schema::table('machines', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }

        if (Schema::hasColumn('stras_prices', 'cost_price')) {
            Schema::table('stras_prices', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }

        if (Schema::hasColumn('tarter_prices', 'cost_price')) {
            Schema::table('tarter_prices', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }
    }
};
