<?php

namespace Database\Factories;

use App\Models\Enterprise;
use App\Models\FundingSource;
use App\Models\SelectionMethod;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bid_submission_end = $this->faker->dateTimeBetween('now', '+1 year');

        $bid_submission_start = $this->faker->dateTimeBetween('-1 year', $bid_submission_end);

        $bid_opening_date = $this->faker->dateTimeBetween($bid_submission_end, $bid_submission_end->modify('+2 months'));

        $start_time = $this->faker->dateTimeBetween('-10 years', 'now');

        $end_time = $this->faker->dateTimeBetween($start_time, $start_time->modify('+10 years'));

        $approve_at = $this->faker->randomElement([$this->faker->dateTime(), null]);

        $decision_number_approve = $approve_at ? 'QDPD-' . $this->faker->numerify('#######') . '/NĐ-' . $this->faker->numerify('###########') . 'N-' . date('Y') : null;

        $status = $approve_at ? $this->faker->randomElement([2, 3]) : 1;

        $fundings = FundingSource::all();
        $enterprises = Enterprise::all();
        $staffs = Staff::all();
        $selection_methods = SelectionMethod::all();
        return [
            'funding_source_id' => $fundings->random()->id,
            'tenderer_id' => $enterprises->random()->id,
            'investor_id' => $enterprises->random()->id,
            'staff_id' => $staffs->random()->id,
            'selection_method_id' => $selection_methods->random()->id,
            'parent_id' => null,
            'decision_number_issued' => 'QDBH-' . $this->faker->numerify('#######') . '/NĐ-' . $this->faker->numerify('###########') . 'N-' . date('Y'),
            'name' => "Dự án " . $this->faker->name(),
            'is_domestic' => $this->faker->boolean(),
            'location' => $this->faker->address(),
            'amount' => $this->faker->randomFloat(2, 10000000.00, 50000000000.00),
            'total_amount' => $this->faker->randomFloat(2, 10000000.00, 50000000000.00),
            'description' => $this->faker->text(),
            'submission_method' => $this->faker->randomElement(['online', 'in_person']),
            'receiving_place' => function (array $attributes) {
                return $attributes['submission_method'] === 'online' ? null : $this->faker->address();
            },
            'bid_submission_start' => $bid_submission_start,
            'bid_submission_end' => $bid_submission_end,
            'bid_opening_date' => $bid_opening_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'approve_at' => $approve_at,
            'decision_number_approve' => $decision_number_approve,
            'status' => $status,
        ];
    }
}
