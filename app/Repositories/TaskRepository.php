<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository extends BaseRepository
{
    public function getModel()
    {
        return Task::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['difficulty_level'])) {
            $query->where('difficulty_level', $data['difficulty_level']);
        }
        if (isset($data['code'])) {
            $query->where('code', $data['code']);
        }
        if (isset($data['employee'])) {
            $query->whereHas('employees', function ($q) use ($data) {
                $q->where('employees.id', $data['employee']);
            });
        }
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function syncEmployee(array $data, $id)
    {
        $task = $this->model->findOrFail($id);
        return $task->employees()->sync($data['employee_id']);
    }

    public function countTask()
    {
        return $this->model->count();
    }

    public function compareRatioDificultyByProject($project_ids) {
        $taskCounts = $this->model
            ->select('difficulty_level', \DB::raw('COUNT(*) as count'))
            ->whereIn('project_id', $project_ids)
            ->groupBy('difficulty_level')
            ->get()
            ->pluck('count', 'difficulty_level')
            ->toArray();

        $easyCount = $taskCounts['easy'] ?? 0;
        $mediumCount = $taskCounts['medium'] ?? 0;
        $hardCount = $taskCounts['hard'] ?? 0;
        $veryHardCount = $taskCounts['very_hard'] ?? 0;

        return [
            'easy' => $easyCount,
            'medium' => $mediumCount,
            'hard' => $hardCount,
            'very_hard' => $veryHardCount,
        ];
    }

}
