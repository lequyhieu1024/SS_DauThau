<?php

namespace Database\Factories;

use App\Models\Enterprise;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BidBond>
 */
class BidbondFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projects = Project::all();
        $enterprises = Enterprise::all();

        $projectWithoutBidBond = $projects->filter(function($project) {
            return !$project->bidbond;
        });

        if ($projectWithoutBidBond->isNotEmpty()) {
            $selectedProject = $projectWithoutBidBond->random();

            return [
                'project_id' => $selectedProject->id,
                'enterprise_id' => $enterprises->random()->id,
                'bond_number' => $this->faker->numerify('###########'),
                'bond_amount' => $this->faker->randomFloat(2, 100000, 100000000),
                'bond_amount_in_words' => $this->faker->streetName(),
                'bond_type' => $this->faker->randomElement([1,2]),
                'issue_date' => $this->faker->date(),
                'expiry_date' => $this->faker->date(),
                'description' => $this->faker->text(),
            ];
        }

        return [];
    }
}
