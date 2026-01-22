<?php

namespace Database\Factories;

use App\Models\Printers;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrintersFactory extends Factory
{
    protected $model = Printers::class;

    public function definition(): array
    {
        return [
            'orderNumber' => 'ORD-' . $this->faker->unique()->numberBetween(10000000, 99999999),
            'customerId' => 1, // Assuming customer 1 exists, or use Customers::inRandomOrder()->first()->id ?? 1
            'machineId' => 1,
            'fileHeight' => $this->faker->numberBetween(10, 200),
            'fileWidth' => $this->faker->numberBetween(10, 200),
            'fileCopies' => $this->faker->numberBetween(1, 10),
            'picInCopies' => 1,
            'pass' => $this->faker->randomElement([2, 4, 6, 8]),
            'meters' => $this->faker->randomFloat(2, 1, 50),
            'status' => 'انتهت الطباعة',
            'paymentStatus' => 'unpaid',
            'designerId' => 1,
            'operatorId' => 1,
            'notes' => $this->faker->sentence(),
            'archive' => 1,
            'timeEndOpration' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
