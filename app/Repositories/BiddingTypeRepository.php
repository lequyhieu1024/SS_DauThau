<?php

namespace App\Repositories;

class BiddingTypeRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\BiddingType::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function createBiddingTpye(array $data)
    {
        return $this->model::create($data);
    }

    public function findBiddingTypeById($id)
    {
        return $this->model->find($id);
    }

    public function updateBiddingTpye(array $data, $id)
    {
        $biddingTpye = $this->model->find($id);
        if ($biddingTpye) {
            $biddingTpye->update($data);
        }
        return $biddingTpye;
    }

    public function deleteBiddingTpye($id)
    {
        $biddingTpye = $this->model->find($id);
        if ($biddingTpye) {
            $biddingTpye->delete();
        }
        return $biddingTpye;
    }
}
