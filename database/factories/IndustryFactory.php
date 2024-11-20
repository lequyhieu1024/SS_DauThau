<?php

namespace Database\Factories;

use App\Models\BusinessActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Industry>
 */
class IndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessActivityTypeIds = BusinessActivityType::pluck('id')->toArray();
        return [
            'business_activity_type_id' => $this->faker->randomElement($businessActivityTypeIds),
            'name' => 'Ngành nghề ' . $this->faker->name(),
            'description' => $this->faker->text(),
            'is_active' => $this->faker->randomElement([true, false]),
        ];
    }
}
