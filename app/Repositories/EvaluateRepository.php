<?php

namespace App\Repositories;
use App\Models\Evaluate;

class EvaluateRepository extends BaseRepository {
    public function getModel()
    {
        return Evaluate::class;
    }

    public function filter($data) {
        $query = $this->model->query();
        //logic filter

        if (isset($data['title'])) {
            $query->where('title', 'like', '%' . $data['title'] . '%');
        }

        if (isset($data['project'])) {
            $query->where('project_id', $data['project']);
        }
        if (isset($data['enterprise'])) {
            $query->where('enterprise_id', $data['enterprise']);
        }

        if (isset($data['score_from'])) {
            $query->where('score', '>=', $data['score_from']);
        }
        if (isset($data['score_to'])) {
            $query->where('score', '<=', $data['score_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }
}
