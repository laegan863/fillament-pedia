<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\CityMunicipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Barangay>
 */
class BarangayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('#########'),
            'city_municipality_code' => CityMunicipality::factory(),
            'name' => fake()->unique()->streetName(),
        ];
    }
}
