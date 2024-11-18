<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enterprise>
 */
class EnterpriseFactory extends Factory
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
            'representative' => $this->faker->name(),
            'avatar' => $this->faker->imageUrl(),
            'phone' => '0' . $this->faker->unique()->numberBetween(100000000, 999999999),
            'address' => $this->faker->address(),
            'website' => $this->faker->url(),
            'description' => $this->faker->text(),
            'establish_date' => $this->faker->date(),
            'registration_date' => $this->faker->date(),
            'registration_number' => $this->faker->randomNumber(),
            'organization_type' => $this->faker->randomElement([1,2]),
            'is_active' => $this->faker->randomElement([true,false]),
            'is_blacklist' => $this->faker->randomElement([true,false]),
        ];
    }
}
