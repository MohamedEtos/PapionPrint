<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Printingprices;  
class PrintingpricesSeedere extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Printingprices = [
            [
            'machineId' => 1,
            'orderId' => 1,
            'pricePerMeter' => 23,
            'totalPrice' => 100,
            'discount' => 10,
            'finalPrice' => 90,
            ],
        ];
        foreach ($Printingprices as $Printingprice) {
            Printingprices::create($Printingprice);
        }
    }
}
