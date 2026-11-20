<?php

namespace Database\Factories;

use App\Models\CityMunicipality;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CityMunicipality>
 */
class CityMunicipalityFactory extends Factory
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
            'region_code' => Region::factory(),
            'province_code' => null,
            'name' => fake()->unique()->city(),
            'is_city' => false,
        ];
    }
}
