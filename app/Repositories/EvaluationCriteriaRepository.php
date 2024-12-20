<?php

namespace App\Repositories;

use App\Models\EvaluationCriteria;

class EvaluationCriteriaRepository extends BaseRepository
{
    public function getModel()
    {
        return EvaluationCriteria::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['project'])) {
            $query->where('project_id', $data['project']);
        }

        if (isset($data['name']) && is_string($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (isset($data['is_active'])) {
            $query->where('is_active', '=', $data['is_active']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function countEvaluationCriteria()
    {
        return [
            'total_evaluation_criterias' => $this->model->count(),
            'total_active_evaluation_criterias' => $this->model->where('is_active', 1)->count(),
            'total_inactive_evaluation_criterias' => $this->model->where('is_active', 0)->count(),
        ];
    }
}
