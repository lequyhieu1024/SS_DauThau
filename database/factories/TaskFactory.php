<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::where('id', '<=', 1000);
        return [
            'name' => $this->faker->name(),
            'project_id' => $project->random()->id,
            'code' => 'refs-' . $this->faker->unique()->randomNumber(10),
            'description' => $this->faker->text(),
            'difficulty_level' => $this->faker->randomElement(['easy', 'medium', 'hard', 'veryhard'])
        ];
    }
}
