<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TarterPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Needle Sizes (Replacing Stras Sizes)
        $needles = ['3mm', '5mm', '7mm', '9mm']; // Assuming standard sequins/needle sizes, user said "Needle Size"
        // Let's use generic sizes if unsure: 6, 8, 10, 12 might also apply but with "Needle" label.
        // I will stick to what user likely expects based on context or use standard.
        // User said "replace grain size with needle size".
        // I'll populate a few common ones.
        $needles = ['1,2', '3,4', '5,6', '7,8,9', ]; // Common needle sizes
        
        foreach ($needles as $size) {
            \App\Models\TarterPrice::firstOrCreate(
                ['size' => $size, 'type' => 'needle'],
                ['price' => 0.020]
            );
        }

        // Paper Prices (3 Sizes) - Same as Stras
        $papers = ['size 24', 'size 32', 'size 40'];
        foreach ($papers as $paper) {
             \App\Models\TarterPrice::firstOrCreate(
                ['size' => $paper, 'type' => 'paper'],
                ['price' => 1.00]
            );
        }

        // Operating Cost (Global)
        \App\Models\TarterPrice::firstOrCreate(
            ['size' => 'operating_cost', 'type' => 'global'],
            ['price' => 2.00] 
        );

        // Machine Time Cost (Per Minute?)
        \App\Models\TarterPrice::firstOrCreate(
            ['size' => 'machine_time_cost', 'type' => 'machine_time_cost'],
            ['price' => 0.50] // Cost per minute/unit
        );
    }
}
