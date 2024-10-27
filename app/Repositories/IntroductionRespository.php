<?php

namespace App\Repositories;

use App\Models\Introduction;

class IntroductionRespository extends BaseRepository
{
    public function getModel()
    {
        return Introduction::class;
    }
    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['introduction']) && is_string($data['introduction'])) {
            $query->where('introduction', 'like', '%' . $data['introduction'] . '%');
        }

        if (isset($data['is_use'])) {
            $query->where('is_use', '=', $data['is_use']);
        }

        return $query->paginate($data['size'] ?? 10);
    }

}

