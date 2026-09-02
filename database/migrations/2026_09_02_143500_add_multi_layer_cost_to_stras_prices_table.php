<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StrasPrice;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        StrasPrice::firstOrCreate(
            ['size' => 'multi_layer_cost', 'type' => 'global'],
            ['price' => 1.00]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        StrasPrice::where('size', 'multi_layer_cost')->where('type', 'global')->delete();
    }
};
