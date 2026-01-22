<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Printers;

class ArchivedOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use Eloquent create method which handles date casting correctly
        \Database\Factories\PrintersFactory::new()->count(1000)->create();
    }
}
