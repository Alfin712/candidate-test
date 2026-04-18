<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                   => $this->faker->company(),
            'primary_contact'        => $this->faker->name(),
            'location'               => $this->faker->city() . ', ' . $this->faker->country(),
            'material_certifications'=> $this->faker->randomElement(['ISO 9001', 'AS9100', 'NADCAP', 'ISO 14001']),
            'last_audit_date'        => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'status'                 => $this->faker->randomElement(['active', 'inactive', 'pending']),
        ];
    }
}
