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

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }
    public function updateAll(array $data)
    {
        return $this->model->where('is_use', true)->update($data);
    }

    public function countActive()
    {
        return $this->model->where('is_use', true)->count();
    }

    public function getCurrentActive()
    {
        return $this->model->where('is_use', true)->first();
    }
    public function countAll()
    {
        return $this->model->count();
    }

    public function getIntroductionLandipage() {
        return $this->model->where('is_use', true)->first();
    }
}

