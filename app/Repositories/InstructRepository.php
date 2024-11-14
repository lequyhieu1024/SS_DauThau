<?php

namespace App\Repositories;

use App\Models\Instruct;

class InstructRepository extends BaseRepository
{
    public function getModel()
    {
        return Instruct::class;
    }
    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['instruct']) && is_string($data['instruct'])) {
            $query->where('instruct', 'like', '%' . $data['instruct'] . '%');
        }

        if (isset($data['is_use'])) {
            $query->where('is_use', '=', $data['is_use']);
        }

        return $query->paginate($data['size'] ?? 10);
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



}

