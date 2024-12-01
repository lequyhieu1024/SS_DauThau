<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\WorkProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TaskProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workProgressIds = WorkProgress::limit(200)->pluck('id')->toArray();
        $taskIds = Task::limit(200)->pluck('id')->toArray();
        return [
            'work_progress_id' => $this->faker->randomElement($workProgressIds),
            'task_id' => $this->faker->randomElement($taskIds),
        ];
    }
}
