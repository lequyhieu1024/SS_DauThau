<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeTask>
 */
class EmployeeTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taskIds = Task::pluck('id')->toArray();
        $employeeIds = Employee::pluck('id')->toArray();

        return [
            'task_id' => $this->faker->randomElement($taskIds),
            'employee_id' => $this->faker->randomElement($employeeIds),
            'feedback' => $this->faker->randomElement(['poor', 'medium', 'good', 'verygood', 'excellent']),
        ];
    }
}
