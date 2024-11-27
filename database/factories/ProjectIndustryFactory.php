<?php

namespace Database\Factories;

use App\Models\Industry;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProjectIndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projectIds = Project::pluck('id')->toArray();
        $industryIds = Industry::limit(10)->pluck('id')->toArray();

        return [
            'project_id' => $this->faker->randomElement($projectIds),
            'industry_id' => $this->faker->randomElement($industryIds),
        ];
    }
}
