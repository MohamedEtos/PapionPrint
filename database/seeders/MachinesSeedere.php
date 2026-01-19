<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Machines;
class MachinesSeedere extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [
            [
                'name' => 'DTF ',
                'type' => 'Printer ',
                'timePrintPerHour' => '19',
            ],
            [
                'name' => 'Sublimation ',
                'type' => 'Printer ',
                'timePrintPerHour' => '50',
            ],
        ];
        foreach ($machines as $machine) {
            Machines::create($machine);
        }
    }
}
