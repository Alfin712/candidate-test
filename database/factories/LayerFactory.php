<?php

namespace Database\Factories;

use App\Models\Layup;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'layup_id'    => Layup::factory(),
            'layer_order' => $this->faker->unique()->numberBetween(1, 50),
            'thickness'   => round($this->faker->randomFloat(2, 0.1, 5.0), 2),
            'width'       => round($this->faker->randomFloat(2, 10, 200), 2),
            'angle'       => $this->faker->randomElement([0, 45, 90, -45, -90, 30, 60]),
        ];
    }
}
