<?php

namespace App\Repositories;

class FeedbackComplaintRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\FeedbackComplaint::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['content'])) {
            $query->where('content', 'like', '%' . $data['content'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }
}
