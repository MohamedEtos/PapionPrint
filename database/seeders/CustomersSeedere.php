<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\customers;
class CustomersSeedere extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'محمد احمد',
                'phone' => '1234567890',
                'whatsAppNumber' => '1234567890',
                'notes' => 'Notes 1',
            ],
            [
                'name' => 'احمد سعد',
                'phone' => '1234567890',
                'whatsAppNumber' => '1234567890',
                'notes' => 'Notes 2',
            ],
        ];
        foreach ($customers as $customer) {
            Customers::create($customer);
        }
    }
}
