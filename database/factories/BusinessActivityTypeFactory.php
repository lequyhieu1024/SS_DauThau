<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessActivityType>
 */
class BusinessActivityTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Loại ngành nghề ' . $this->faker->name(),
            'description' => $this->faker->text(),
            'is_active' => $this->faker->randomElement([true, false]),
        ];
    }
}
