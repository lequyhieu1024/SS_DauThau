<?php

namespace App\Repositories;

use App\Models\Support;

class SupportRepository extends BaseRepository
{

    public function getModel()
    {
        return Support::class;
    }

    /**
     * @param $data
     * @return mixed
     * GET ALL + filter
     */

    public function filter($data)
    {
        $query = $this->model->query();
        // logic loc du an

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

}
