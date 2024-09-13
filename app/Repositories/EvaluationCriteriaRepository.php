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
        $query = $this->model->query(); // ->project()

        // if (isset($data['project_ids']) && is_array($data['project_ids'])) {
        //     $data['project_ids'] = array_filter($data['project_ids'], function ($value) {
        //         return $value !== null;
        //     });

        //     if (!empty($data['project_ids'])) {
        //         $query->whereHas('projects', function ($query) use ($data) {
        //             $query->whereIn('project_id', $data['project_ids']);
        //         });
        //     }
        // }

        if (isset($data['name']) && is_string($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (isset($data['is_active'])) {
            $query->where('is_active', '=', $data['is_active']);
        }

        return $query->paginate($data['size'] ?? 10);
    }
}
