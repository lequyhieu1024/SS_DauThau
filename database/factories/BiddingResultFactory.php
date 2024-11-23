<?php

namespace Database\Factories;

use App\Models\BidDocument;
use App\Models\Enterprise;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BiddingResult>
 */
class BiddingResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projectIds = Project::pluck('id')->toArray();
        $enterpriseIds = Enterprise::pluck('id')->toArray();
        $bidDocumentIds = BidDocument::pluck('id')->toArray();
        return [
            'project_id' => $this->faker->randomElement($projectIds),
            'enterprise_id' => $this->faker->randomElement($enterpriseIds), 
            'bid_document_id' => $this->faker->randomElement($bidDocumentIds), 
            'win_amount' => $this->faker->randomFloat(2, 100000, 10000000), 
            'decision_number' => $this->faker->unique()->numerify('##########'), 
            'decision_date' => $this->faker->dateTimeBetween('-2 years', 'now'), 
        ];
    }
}
