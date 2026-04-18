<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name'        => 'LU-' . strtoupper($this->faker->bothify('??###')),
            'description' => $this->faker->sentence(),
        ];
    }
}
