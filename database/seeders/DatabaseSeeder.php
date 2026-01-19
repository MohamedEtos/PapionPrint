<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Machines;
use App\Models\customers;
use App\Models\Printers;
use App\Models\Printingprices;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            MachinesSeedere::class,
            CustomersSeedere::class,
            PrintersSeedere::class,
            PrintingpricesSeedere::class,
        ]);
    }
}
