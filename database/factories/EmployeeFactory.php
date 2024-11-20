<?php

namespace Database\Factories;

use App\Models\Enterprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enterprises = Enterprise::all();
        return [
            'enterprise_id' => $enterprises->random()->id,
            'code' => 'NV' . $this->faker->unique()->randomNumber(8, true),
            'avatar' => $this->faker->imageUrl(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'birthday' => $this->faker->date(),
            'gender' => $this->faker->randomElement([true, false]),
            'taxcode' => $this->faker->numerify('###########'),
            'education_level' => $this->faker->randomElement(['primary_school', 'secondary_school', 'high_school', 'college', 'university', 'after_university']),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'salary' => $this->faker->numberBetween(1000000, 100000000000),
            'address' => $this->faker->address(),
            'status' => $this->faker->randomElement(['doing', 'pause', 'leave']),
        ];
    }
}
