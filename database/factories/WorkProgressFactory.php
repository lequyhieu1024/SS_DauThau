<?php

namespace Database\Factories;

use App\Models\BiddingResult;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkProgress>
 */
class WorkProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $biddingResultIds = BiddingResult::limit(300)->pluck('id')->toArray();

        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');

        return [
            'bidding_result_id' => $this->faker->randomElement($biddingResultIds),
            'name' => $this->faker->words(3, true), 
            'progress' => $this->faker->randomFloat(2, 0, 100), 
            'expense' => $this->faker->randomFloat(2, 100000, 100000000), 
            'start_date' => Carbon::parse($startDate)->format('Y-m-d'),
            'end_date' => Carbon::parse($endDate)->format('Y-m-d'),
            'feedback' => $this->faker->randomElement(['poor', 'medium', 'good', 'verygood', 'excellent']),
            'description' => $this->faker->paragraph, 
        ];
    }
}
