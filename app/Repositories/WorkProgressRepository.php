<?php

namespace App\Repositories;
use App\Models\WorkProgress;

class WorkProgressRepository extends BaseRepository {
    public function getModel()
    {
        return WorkProgress::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        return $query->paginate($data['size'] ?? 10);
    }

    public function syncTaskProgresses(array $data, $id)
    {
        $progress = $this->model->findOrFail($id);
        return $progress->taskProgresses()->sync($data['task_ids']);
    }
}
