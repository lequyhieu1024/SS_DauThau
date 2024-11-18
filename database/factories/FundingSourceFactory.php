<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FundingSource>
 */
class FundingSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'code' => 'NTT' . strtoupper($this->faker->unique()->bothify('???###')),
            'type' => $this->faker->randomElement(['Chính phủ', 'Tư nhân', 'Quốc tế']),
            'is_active' => $this->faker->randomElement([true, false]),
        ];
    }
}
