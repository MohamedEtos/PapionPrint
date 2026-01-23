<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RollpressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if there are any printers to link to, otherwise use null
        $printer = DB::table('printers')->first();
        $orderId = $printer ? $printer->id : null;

        // Clear existing data to avoid duplicates if re-running
        DB::table('rollpresses')->truncate();

        DB::table('rollpresses')->insert([
            [
                'orderId' => $orderId,
                'fabrictype' => 'ستاتان',
                'fabricsrc' => 'source1',
                'fabriccode' => 'CODE123',
                'fabricwidth' => 150.0,
                'meters' => 50.5,
                'status' => false, // In Progress
                'paymentstatus' => false,
                'papyershild' => 10.0,
                'price' => 500.0,
                'notes' => 'Test Note 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'orderId' => $orderId,
                'fabrictype' => 'حرير',
                'fabricsrc' => 'source2',
                'fabriccode' => 'CODE456',
                'fabricwidth' => 140.0,
                'meters' => 120.0,
                'status' => true, // Completed
                'paymentstatus' => true,
                'papyershild' => 15.0,
                'price' => 1200.0,
                'notes' => 'Test Note 2',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
