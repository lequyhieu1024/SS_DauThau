<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'phone' => '0' . $this->faker->unique()->numberBetween(100000000, 999999999),
            'avatar' => $this->faker->imageUrl(),
            'birthday' => $this->faker->date(),
            'gender' => $this->faker->randomElement([1, 0]),
        ];
    }
}
