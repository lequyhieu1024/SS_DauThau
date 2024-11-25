<?php

namespace Database\Factories;

use App\Models\Enterprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reputation>
 */
class ReputationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enterpriseIds = Enterprise::limit(25)->pluck('id')->toArray();
        $number_of_blacklist = $this->faker->numberBetween(0, 100);
        $number_of_ban = $this->faker->numberBetween(0, 100 - $number_of_blacklist);
        $prestige_score = 100 - ($number_of_blacklist + $number_of_ban);

        return [
            'enterprise_id' => $this->faker->randomElement($enterpriseIds),
            'number_of_blacklist' => $number_of_blacklist,
            'number_of_ban' => $number_of_ban,
            'prestige_score' => $prestige_score,
        ];
    }
}
