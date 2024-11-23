<?php

namespace App\Repositories;
 
use App\Models\QuestionAnswer;

class QuestionAnswerRepository extends BaseRepository{

    public function getModel()
    {
        return QuestionAnswer::class;
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

        if (isset($data['status'])) {
            $query->where('status', '=', $data['status']);
        }

        return $query->paginate($data['size'] ?? 10);
    }
}