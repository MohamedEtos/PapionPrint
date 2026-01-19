<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Printers;
class PrintersSeedere extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $printers = [
            [
                'orderNumber' => '1',
                'customerId' => '1',
                'machineId' => '1',
                'fileHeight' => '10',
                'fileWidth' => '10',
                'fileCopies' => '10',
                'picInCopies' => '10',
                'pass' => '10',
                'meters' => '10',
                'status' => '10',
                'designerId' => '1',
                'operatorId' => '1',
                'notes' => 'Notes 1',
                'archive' => '1',
                'timeEndOpration' => '2026-01-01 10:00:00',
            ],
            [
                'orderNumber' => '2',
                'customerId' => '2',
                'machineId' => '2',
                'fileHeight' => '20',
                'fileWidth' => '20',
                'fileCopies' => '20',
                'picInCopies' => '20',
                'pass' => '20',
                'meters' => '20',
                'status' => '20',
                'designerId' => '1',
                'operatorId' => '1',
                'notes' => 'Notes 2',
                'archive' => '2',
                'timeEndOpration' => '2026-01-01 10:00:00',
            ],
        ];
        foreach ($printers as $printer) {
            Printers::create($printer);
        }
    }
}
