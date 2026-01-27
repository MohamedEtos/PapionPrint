<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StrasPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = ['6', '8', '10', '12'];
        
        foreach ($sizes as $size) {
            \App\Models\StrasPrice::firstOrCreate(
                ['size' => $size, 'type' => 'stras'],
                ['price' => 0.020]
            );
        }

        // Paper Prices (3 Sizes)
        $papers = ['size 24', 'size 32', 'size 40'];
        foreach ($papers as $paper) {
             \App\Models\StrasPrice::firstOrCreate(
                ['size' => $paper, 'type' => 'paper'],
                ['price' => 1.00]
            );
        }

        // Operating Cost (Global)
        \App\Models\StrasPrice::firstOrCreate(
            ['size' => 'operating_cost', 'type' => 'global'],
            ['price' => 2.00] // Default value
        );
    }
}
