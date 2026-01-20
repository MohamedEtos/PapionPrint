<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrdersImg;
class OrdersImgSeedere extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ordersImgs = [
            [
                'orderId' => '1',
                'path' => 'images/img1.jpg',
                'type' => 'image',
            ],
            [
                'orderId' => '2',
                'path' => 'images/img2.jpg',
                'type' => 'image',
            ],
            [
                'orderId' => '2',
                'path' => 'images/img3.jpg',
                'type' => 'image',
            ],
        ];
        foreach ($ordersImgs as $orderImg) {
            OrdersImg::create($orderImg);
        }
    }
}
