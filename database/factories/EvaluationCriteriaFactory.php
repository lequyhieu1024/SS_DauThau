<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationCriteria>
 */
class EvaluationCriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projects = Project::where('id', '<', 1000)->get();
        return [
            'project_id' => $projects->random()->id,
            'name' => 'Tiêu chí ' . $this->faker->name(),
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'description' => $this->faker->text(),
            'is_active' => $this->faker->randomElement([true, false]),
        ];
    }
}
