<?php

namespace Database\Factories;

use App\Models\BidBond;
use App\Models\Enterprise;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BidDocument>
 */
class BidDocumentFactory extends Factory
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
        $bidBond = BidBond::all();
        return [
            'project_id' => $projects->random()->id,
            'enterprise_id' => $enterprises->random()->id,
            'bid_bond_id' => $bidBond->random()->id,
            'submission_date' => $this->faker->date(),
            'bid_price' => $this->faker->randomFloat(2, 100000, 100000000),
            'implementation_time' => $this->faker->dateTime(),
            'validity_period' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement([1, 2, 3, 4]),
            'note' => $this->faker->text(),
        ];
    }
}
