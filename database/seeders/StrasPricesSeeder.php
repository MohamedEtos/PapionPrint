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
            \App\Models\StrasPrice::create([
                'size' => $size,
                'price' => 0.020,
            ]);
        }
    }
}
