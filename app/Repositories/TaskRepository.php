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
}
