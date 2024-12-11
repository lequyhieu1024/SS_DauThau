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

        if (isset($data['sender'])) {
            $query->where(function ($query) use ($data) {
                $query->whereHas('user', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['sender'] . '%');
                })->orWhereNull('user_id');
            });
        }


        if (isset($data['title'])) {
            $query->where('title', 'like', '%' . $data['title'] . '%');
        }

        if (isset($data['email'])) {
            $query->where('email', 'like', '%' . $data['email'] . '%');
        }

        if (isset($data['phone'])) {
            $query->where('phone', 'like', '%' . $data['phone'] . '%');
        }

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function countSupport()
    {
        return $this->model->count();
    }

}
